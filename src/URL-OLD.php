<?php
    namespace Nobanno;

    class URL{

        
            /**
     * Access an array index, retrieving the value stored there if it
     * exists or a default if it does not. This function allows you to
     * concisely access an index which may or may not exist without
     * raising a warning.
     *
     * @param  array  $var     Array value to access
     * @param  mixed  $default Default value to return if the key is not
     *                         present in the array
     * @return mixed
     */
    public static function array_get(&$var, $default = null)
    {
        if (isset($var)) {
            return $var;
        }

        return $default;
    }

        /**
     * URL constants as defined in the PHP Manual under "Constants usable with
     * http_build_url()".
     *
     * @see http://us2.php.net/manual/en/http.constants.php#http.constants.url
     */
    const HTTP_URL_REPLACE = 1;
    const HTTP_URL_JOIN_PATH = 2;
    const HTTP_URL_JOIN_QUERY = 4;
    const HTTP_URL_STRIP_USER = 8;
    const HTTP_URL_STRIP_PASS = 16;
    const HTTP_URL_STRIP_AUTH = 32;
    const HTTP_URL_STRIP_PORT = 64;
    const HTTP_URL_STRIP_PATH = 128;
    const HTTP_URL_STRIP_QUERY = 256;
    const HTTP_URL_STRIP_FRAGMENT = 512;
    const HTTP_URL_STRIP_ALL = 1024;

        /**
     * Build a URL.
     *
     * The parts of the second URL will be merged into the first according to
     * the flags argument.
     *
     * @author Jake Smith <theman@jakeasmith.com>
     * @see https://github.com/jakeasmith/http_build_url/
     *
     * @param mixed $url     (part(s) of) an URL in form of a string or
     *                       associative array like parse_url() returns
     * @param mixed $parts   same as the first argument
     * @param int   $flags   a bitmask of binary or'ed HTTP_URL constants;
     *                       HTTP_URL_REPLACE is the default
     * @param array $new_url if set, it will be filled with the parts of the
     *                       composed url like parse_url() would return
     * @return string
     */
    public static function http_build_url($url, $parts = array(), $flags = self::HTTP_URL_REPLACE, &$new_url = array())
    {
        is_array($url) || $url = parse_url($url);
        is_array($parts) || $parts = parse_url($parts);

        isset($url['query']) && is_string($url['query']) || $url['query'] = null;
        isset($parts['query']) && is_string($parts['query']) || $parts['query'] = null;

        $keys = array('user', 'pass', 'port', 'path', 'query', 'fragment');

        // HTTP_URL_STRIP_ALL and HTTP_URL_STRIP_AUTH cover several other flags.
        if ($flags & self::HTTP_URL_STRIP_ALL) {
            $flags |= self::HTTP_URL_STRIP_USER | self::HTTP_URL_STRIP_PASS
                | self::HTTP_URL_STRIP_PORT | self::HTTP_URL_STRIP_PATH
                | self::HTTP_URL_STRIP_QUERY | self::HTTP_URL_STRIP_FRAGMENT;
        } elseif ($flags & self::HTTP_URL_STRIP_AUTH) {
            $flags |= self::HTTP_URL_STRIP_USER | self::HTTP_URL_STRIP_PASS;
        }

        // Schema and host are alwasy replaced
        foreach (array('scheme', 'host') as $part) {
            if (isset($parts[$part])) {
                $url[$part] = $parts[$part];
            }
        }

        if ($flags & self::HTTP_URL_REPLACE) {
            foreach ($keys as $key) {
                if (isset($parts[$key])) {
                    $url[$key] = $parts[$key];
                }
            }
        } else {
            if (isset($parts['path']) && ($flags & self::HTTP_URL_JOIN_PATH)) {
                if (isset($url['path']) && substr($parts['path'], 0, 1) !== '/') {
                    $url['path'] = rtrim(
                        str_replace(basename($url['path']), '', $url['path']),
                        '/'
                    ) . '/' . ltrim($parts['path'], '/');
                } else {
                    $url['path'] = $parts['path'];
                }
            }

            if (isset($parts['query']) && ($flags & self::HTTP_URL_JOIN_QUERY)) {
                if (isset($url['query'])) {
                    parse_str($url['query'], $url_query);
                    parse_str($parts['query'], $parts_query);

                    $url['query'] = http_build_query(
                        array_replace_recursive(
                            $url_query,
                            $parts_query
                        )
                    );
                } else {
                    $url['query'] = $parts['query'];
                }
            }
        }

        if (isset($url['path']) && substr($url['path'], 0, 1) !== '/') {
            $url['path'] = '/' . $url['path'];
        }

        foreach ($keys as $key) {
            $strip = 'HTTP_URL_STRIP_' . strtoupper($key);
            if ($flags & constant('utilphp\\util::' . $strip)) {
                unset($url[$key]);
            }
        }

        $parsed_string = '';

        if (isset($url['scheme'])) {
            $parsed_string .= $url['scheme'] . '://';
        }

        if (isset($url['user'])) {
            $parsed_string .= $url['user'];

            if (isset($url['pass'])) {
                $parsed_string .= ':' . $url['pass'];
            }

            $parsed_string .= '@';
        }

        if (isset($url['host'])) {
            $parsed_string .= $url['host'];
        }

        if (isset($url['port'])) {
            $parsed_string .= ':' . $url['port'];
        }

        if (!empty($url['path'])) {
            $parsed_string .= $url['path'];
        } else {
            $parsed_string .= '/';
        }

        if (isset($url['query'])) {
            $parsed_string .= '?' . $url['query'];
        }

        if (isset($url['fragment'])) {
            $parsed_string .= '#' . $url['fragment'];
        }

        $new_url = $url;

        return $parsed_string;
    }


    /**
     * Add or remove query arguments to the URL.
     *
     * @param  mixed  $newKey          Either newkey or an associative array
     * @param  mixed  $newValue        Either newvalue or oldquery or uri
     * @param  mixed  $uri             URI or URL to append the queru/queries to.
     * @return string
     */
    public static function add_query_arg($newKey, $newValue = null, $uri = null)
    {
        // Was an associative array of key => value pairs passed?
        if (is_array($newKey)) {
            $newParams = $newKey;

            // Was the URL passed as an argument?
            if (!is_null($newValue)) {
                $uri = $newValue;
            } elseif (!is_null($uri)) {
                $uri = $uri;
            } else {
                $uri = self::array_get($_SERVER['REQUEST_URI'], '');
            }
        } else {
            $newParams = array($newKey => $newValue);

            // Was the URL passed as an argument?
            $uri = is_null($uri) ? self::array_get($_SERVER['REQUEST_URI'], '') : $uri;
        }

        // Parse the URI into it's components
        $puri = parse_url($uri);

        if (isset($puri['query'])) {
            parse_str($puri['query'], $queryParams);
            $queryParams = array_merge($queryParams, $newParams);
        } elseif (isset($puri['path']) && strstr($puri['path'], '=') !== false) {
            $puri['query'] = $puri['path'];
            unset($puri['path']);
            parse_str($puri['query'], $queryParams);
            $queryParams = array_merge($queryParams, $newParams);
        } else {
            $queryParams = $newParams;
        }

        // Strip out any query params that are set to false.
        // Properly handle valueless parameters.
        foreach ($queryParams as $param => $value) {
            if ($value === false) {
                unset($queryParams[$param]);
            } elseif ($value === null) {
                $queryParams[$param] = '';
            }
        }

        // Re-construct the query string
        $puri['query'] = http_build_query($queryParams);

        // Strip = from valueless parameters.
        $puri['query'] = preg_replace('/=(?=&|$)/', '', $puri['query']);


        // Re-construct the entire URL
        $nuri = self::http_build_url($puri);

        // Make the URI consistent with our input
        if ($nuri[0] === '/' && strstr($uri, '/') === false) {
            $nuri = substr($nuri, 1);
        }

        if ($nuri[0] === '?' && strstr($uri, '?') === false) {
            $nuri = substr($nuri, 1);
        }

        return rtrim($nuri, '?');
    }

    /**
     * Removes an item or list from the query string.
     *
     * @param  string|array  $keys Query key or keys to remove.
     * @param  bool          $uri  When false uses the $_SERVER value
     * @return string
     */
    public static function remove_query_arg($keys, $uri = null)
    {
        if (is_array($keys)) {
            return self::add_query_arg(array_combine($keys, array_fill(0, count($keys), false)), $uri);
        }

        return self::add_query_arg(array($keys => false), $uri);
    }

















    /**
     * Checks to see if the page is being server over SSL or not
     *
     * @return boolean
     */
    public static function isHTTPS($trust_proxy_headers = false)
    {
        // Check standard HTTPS header
        if (isset($_SERVER['HTTPS']) && !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') {
           return isset($_SERVER['HTTPS']) && !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off';
        }

        // Check proxy headers if allowed
        return $trust_proxy_headers && isset($_SERVER['X-FORWARDED-PROTO']) && $_SERVER['X-FORWARDED-PROTO'] == 'https';

        // Default to not SSL
        return false;
    }

    /**
     * Return the current URL.
     *
     * @return string
     */
    public static function getCurrentURL():string
    {
        $url = '';

        // Check to see if it's over https
        $isHTTPS = self::isHTTPS();
        if ($isHTTPS) {
            $url .= 'https://';
        } else {
            $url .= 'http://';
        }

        // Was a username or password passed?
        if (isset($_SERVER['PHP_AUTH_USER'])) {
            $url .= $_SERVER['PHP_AUTH_USER'];

            if (isset($_SERVER['PHP_AUTH_PW'])) {
                $url .= ':' . $_SERVER['PHP_AUTH_PW'];
            }

            $url .= '@';
        }


        // We want the user to stay on the same host they are currently on,
        // but beware of security issues
        // see http://shiflett.org/blog/2006/mar/server-name-versus-http-host
        $url .= $_SERVER['HTTP_HOST'];

        $port = $_SERVER['SERVER_PORT'];

        // Is it on a non standard port?
        if ($isHTTPS && ($port != 443)) {
            $url .= ':' . $_SERVER['SERVER_PORT'];
        } elseif (!$isHTTPS && ($port != 80)) {
            $url .= ':' . $_SERVER['SERVER_PORT'];
        }

        // Get the rest of the URL
        if (!isset($_SERVER['REQUEST_URI'])) {
            // Microsoft IIS doesn't set REQUEST_URI by default
            $url .= $_SERVER['PHP_SELF'];

            if (isset($_SERVER['QUERY_STRING'])) {
                $url .= '?' . $_SERVER['QUERY_STRING'];
            }
        } else {
            $url .= $_SERVER['REQUEST_URI'];
        }

        return $url;
    }
    }
