<?php
namespace Nobanno;
use InvalidArgumentException;
final class CodeGenerator
{

    public static function generate( $id, $secret, $length = 10 , bool $allowLeadingZero = false):string{
        if($allowLeadingZero){
            return self::generateUserCodeDeterministic($id, $secret, $length);
        }
        else{
            return self::generateUserCodeNoLeadingZeroStrict($id, $secret, $length);
        }
    }

    /**
     * Deterministic, collision-free numeric code for $id within 10^length.
     * Min: 0, Max: 9,999,999,999, Count: 10,000,000,000 (when length = 10).
     * Requires: 0 <= $id < 10^length.
     * Uses a 6-round Feistel network to produce a permutation over the numeric space,
     * making codes appear random while preserving uniqueness for the valid range.
     *
     * @param int $id
     * @param string $secret
     * @param int $length
     * @return string
     * @throws InvalidArgumentException
     */
    private static function generateUserCodeDeterministic($id, $secret, $length = 10):string
    {
        if (!is_int($id) || $id < 0) {
            throw new InvalidArgumentException('ID must be a non-negative integer.');
        }

        if (!is_int($length) || $length < 2 || ($length % 2) !== 0) {
            throw new InvalidArgumentException('Length must be an even integer >= 2.');
        }

        if ($length > 18) {
            throw new InvalidArgumentException('Length must be <= 18 for 64-bit integer safety.');
        }

        $mod = self::pow10($length);
        if ($id >= $mod) {
            throw new InvalidArgumentException('ID must be less than 10^length for the selected code length.');
        }

        $halfDigits = intdiv($length, 2);
        $halfMod = self::pow10($halfDigits);
        $left = intdiv($id, $halfMod);
        $right = $id % $halfMod;

        // Feistel network produces a permutation over 10^length when using equal halves.
        for ($round = 0; $round < 6; $round++) {
            $roundInput = $right . ':' . $round;
            $hash = hash_hmac('sha256', $roundInput, $secret, false);
            $f = hexdec(substr($hash, 0, 8)) % $halfMod;

            $newLeft = $right;
            $newRight = ($left + $f) % $halfMod;

            $left = $newLeft;
            $right = $newRight;
        }

        $code = ($left * $halfMod) + $right;
        return str_pad((string)$code, $length, '0', STR_PAD_LEFT);
    }

    /**
     * Deterministic numeric code with no leading zero.
     * Min: 1,000,000,000, Max: 9,999,999,999, Count: 9,000,000,000 (when length = 10).
     * Requires: 0 <= $id < 9 * 10^(length-1).
     * Maps $id into a non-leading-zero space by permuting the rank and then
     * assembling a first digit in 1..9 with a (length-1)-digit tail.
     *
     * @param int $id
     * @param string $secret
     * @param int $length
     * @return string
     * @throws InvalidArgumentException
     */
    private static function generateUserCodeNoLeadingZeroStrict($id, $secret, $length = 10):string
    {
        if (!is_int($id) || $id < 0) {
            throw new InvalidArgumentException('ID must be a non-negative integer.');
        }

        if (!is_int($length) || $length < 2) {
            throw new InvalidArgumentException('Length must be an integer >= 2.');
        }

        if ($length > 18) {
            throw new InvalidArgumentException('Length must be <= 18 for 64-bit integer safety.');
        }

        $tailMod = self::pow10($length - 1);
        $maxIds = 9 * $tailMod;
        if ($id >= $maxIds) {
            throw new InvalidArgumentException('ID must be less than 9 * 10^(length-1) for no-leading-zero codes.');
        }

        $rank = self::permuteNoLeadingZeroRank($id, $secret, $length);
        $leading = intdiv($rank, $tailMod) + 1;
        $tail = $rank % $tailMod;

        $code = ($leading * $tailMod) + $tail;
        return str_pad((string)$code, $length, '0', STR_PAD_LEFT);
    }

    /**
     * Internal permutation over the no-leading-zero rank space.
     * Uses a Feistel network over a (9 * 10^(length-1)) sized domain split into
     * two halves whose sizes alternate each round to keep the permutation valid.
     *
     * @param int $rank
     * @param string $secret
     * @param int $length
     * @return int
     */
    private static function permuteNoLeadingZeroRank($rank, $secret, $length)
    {
        $tailDigits = $length - 1;
        $leftDigits = intdiv($tailDigits, 2);
        $rightDigits = $tailDigits - $leftDigits;

        $leftMod = 9 * self::pow10($leftDigits);
        $rightMod = self::pow10($rightDigits);

        $left = intdiv($rank, $rightMod);
        $right = $rank % $rightMod;

        for ($round = 0; $round < 6; $round++) {
            $roundInput = $right . ':' . $round;
            $hash = hash_hmac('sha256', $roundInput, $secret, false);
            $f = hexdec(substr($hash, 0, 8)) % $leftMod;

            $newLeft = $right;
            $newRight = ($left + $f) % $leftMod;

            $left = $newLeft;
            $right = $newRight;

            $tmp = $leftMod;
            $leftMod = $rightMod;
            $rightMod = $tmp;
        }

        return ($left * $rightMod) + $right;
    }

    /**
     * Integer power of 10 for safe length-based math.
     * Avoids floating point rounding for large powers.
     *
     * @param int $digits
     * @return int
     */
    private static function pow10($digits)
    {
        $value = 1;
        for ($i = 0; $i < $digits; $i++) {
            $value *= 10;
        }
        return $value;
    }

    public static function createSecret(): string
    {
        // sample value "1004f84049f133c936f4b69fb382559f8c6a36d68d95a5c880ab852440a32e25";
        return  bin2hex(random_bytes(32));
    }

    public static function benchMark($secret)
    {
        $iterations = 1000;
        $start = microtime(true);
        for ($i = 1; $i <= $iterations; $i++) {
            self::generateUserCodeDeterministic($i, $secret, 10);
        }
        $elapsedDet = microtime(true) - $start;

        $start = microtime(true);
        for ($i = 1; $i <= $iterations; $i++) {
            self::generateUserCodeNoLeadingZeroStrict($i, $secret, 10);
        }
        $elapsedNoZeroStrict = microtime(true) - $start;

        echo 'Deterministic: ' . $elapsedDet . ' sec<br>';
        echo 'NoLeadingZeroStrict: ' . $elapsedNoZeroStrict . ' sec<br>';
    }
}
