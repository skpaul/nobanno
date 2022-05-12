<?php
require_once("../Required.php");

Required::Logger();


?>

<!DOCTYPE html>
<html>

<head>
    <title>Customer Care Center - <?= ORGANIZATION_FULL_NAME ?></title>
    <!--[if lt IE 9]>
            <script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script>
            <![endif]-->

    <?php
    Required::metaTags()->favicon()->teletalkCSS()->sweetModalCSS()->bootstrapGrid();
    ?>


    <style>
        /* Style the buttons that are used to open and close the accordion panel */
        .accordion {
            background-color: #fff;
            color: #444;
            cursor: pointer;
            padding: 18px;
            width: 100%;
            text-align: left;
            border: none;
            outline: none;
            transition: 0.4s;
            box-shadow: 0 0 4px #807e7e;
            margin-top: 17px;
            /* margin-bottom:10px; */
        }

        /* Add a background color to the button if it is clicked on (add the .active class with JS), and when you move the mouse over it (hover) */
        .active,
        .accordion:hover {
            background-color: rgba(0, 0, 0, 0.05);
        }

        /* Style the accordion panel. Note: hidden by default */
        .panel {
            padding: 0 18px;
            background-color: white;
            padding-top: 11px;
            padding-bottom: 11px;
            display: none;
            overflow: hidden;
            box-shadow: 0 0 4px #807e7e;
            /* -webkit-box-shadow: 0 -3px 3px -3px black, 3px 0px 3px -3px black, -3px 0px 3px -3px black;
            -moz-box-shadow:    0 -3px 3px -3px black, 3px 0px 3px -3px black, -3px 0px 3px -3px black;
            box-shadow:         0 -3px 3px -3px black, 3px 0px 3px -3px black, -3px 0px 3px -3px black; */
            margin-bottom: 10px;
        }

        .panel p{
            line-height: 1.4 !important;
        }
        .accordion:after {
            content: "\02795";
            /* Unicode character for "plus" sign (+) */
            font-size: 13px;
            color: #777;
            float: right;
            margin-left: 5px;
        }

        .active:after {
            content: "\2796";
            /* Unicode character for "minus" sign (-) */
        }
    </style>

















</head>

<body>
    <!-- <div id="version"></div> -->
    <div class="master-wrapper">
        <header>
            <?php
            require_once(ROOT_DIRECTORY . "/inc/header.php");
            echo prepareHeader(ORGANIZATION_FULL_NAME);
            ?>
        </header>
        <main>
            <div class="container">
                <h2 class="text-center margin-bottom-25">Customer Care Center</h2>

                <div style="margin: 10px;">
                    <!-- Dhaka Zone -->
                    <button class="accordion">Dhaka</button>
                    <div class="panel">
                        <button class="accordion">Banani</button>
                        <div class="panel">
                            <p>
                                Banani Customer Care Center, <br>
                                Banani Post Office, <br>
                                Kamal Ataturk Avenue, <br>
                                Banani, Dhaka <br>
                                Mobile: 01550157750 (9 AM to 5 PM)
                            </p>
                            <p></p>
                        </div>
                        <button class="accordion">Uttara</button>
                        <div class="panel">
                            <p>Uttara Customer Care Center,4 Shahajalal Avenue (BTCL Exchange), Sector#06, Uttara, Dhaka-1230, Mobile: 01550150061 (9 AM to 5 PM)</p>
                        </div>
                        <button class="accordion">Bimanbandar</button>
                        <div class="panel">
                            <p>Airport Customer Care Center , Hazrat Shahjalal International Airport, Kurmitola, Dhaka 1206, Bangladesh, Mobile: 01550157707 (9 AM to 5 PM)</p>
                        </div>
                        <button class="accordion">Dhanmondi</button>
                        <div class="panel">
                            <p>Dhanmondi Customer Care Center , Dr. Refatullah's Happy Arcade (2nd floor) House # 3, Road # 3, Dhanmondi, Dhaka, Mobile: 01550150073 (9 AM to 5 PM)</p>
                        </div>
                        <button class="accordion">Paltan</button>
                        <div class="panel">
                            <p>Palton Customer Care Center , Surma Tower (1st Floor) 59/2 Purana Palton, Dhaka, Mobile: 01550150074 (9 AM to 5 PM)</p>
                        </div>
                        <button class="accordion">Mirpur</button>
                        <div class="panel">
                            <p>Mirpur Customer Care Center , Akhanda Tower (Press Market) Block-Kha, Road-1, Plot-12, Section-6, Mirpur-10, Senpara Parbota, Dhaka., Mobile: 01550150098 (9 AM to 5 PM)</p>
                        </div>
                        <button class="accordion">Tongi</button>
                        <div class="panel">
                            <p>Tongi Customer Care Center, TSS Bhavon, Tongi, Mobile: 01550157780 (9 AM to 5 PM)</p>
                        </div>
                        <button class="accordion">Ramna</button>
                        <div class="panel">
                            <p>Ramna Customer Care Experience Center, Ramna BTCL Compound, Ground Floor, Gulistan, Ramna, Dhaka-1000, Mobile: 01550157784 (9 AM to 5 PM)</p>
                        </div>
                        <button class="accordion">Jatrabari</button>
                        <div class="panel">
                            <p>Jatrabari Customer Care Center, 33/2 Uttor Jatrabari, Nowab Stone Tower, 1st Floor, Dhaka-1204, Mobile: 01550157783 (9 AM to 5 PM)</p>
                        </div>
                        <button class="accordion">Mohammadpur</button>
                        <div class="panel">
                            <p>Shamoli Customer Care Center, Laila Plaza, H#27/1/A, Road 3, Ground Floor, Shamoli, Dhaka-1217, Mobile: 01550150004 (9 AM to 5 PM)</p>
                        </div>
                        <button class="accordion">Kotwali</button>
                        <div class="panel">
                            <p>Sadarghat Customer Care Center, Nagar Siddique, Shop # 14 (Ground Floor), Johnson Road, Sadarghat, Dhaka-1000, Mobile: 01550157799 (9 AM to 5 PM)</p>
                        </div>
                        <button class="accordion">Savar</button>
                        <div class="panel">
                            <p>Savar Customer Care Center, BTCL Telephone Bhaban Savar,Dhaka-1340 (Near Savar Bus Stand), Mobile: 01550150054 (9 AM to 5 PM)</p>
                        </div>
                        <button class="accordion">Lalbagh</button>
                        <div class="panel">
                            <p>Azimpur Customer Care Center, Opposite of New Market Main Gate, BTCL card centre, New Market, Azimpur, Dhaka, Mobile: 01550150024 (9 AM to 5 PM)</p>
                        </div>
                        <button class="accordion">Badda</button>
                        <div class="panel">
                            <p>Badda Customer Care Center, Holland Centre, Shop# 218, 2nd Floor (Mobile Market), Middle Badda, Dhaka, Mobile: 01550157798 (9 AM to 5 PM)</p>
                        </div>
                        <button class="accordion">Tejgaon</button>
                        <div class="panel">
                            <p>Bashundhara City Customer Care Center, Level # 3, Shop # 05, Block # B Bashundhara City, Panthopath, Dhaka-1205, Mobile: 01550150025 (9 AM to 5 PM)</p>
                        </div>
                        <button class="accordion">Keraniganj</button>
                        <div class="panel">
                            <p>Keranigong Customer Care Center, Hasnabad, 1st Floor, Sajeda Bhaban, South Karanigong, Dhaka-1311, Mobile: 01550150007 (9 AM to 5 PM)</p>
                        </div>
                        <button class="accordion">Bhatara</button>
                        <div class="panel">
                            <p>Jamuna Future Park Customer Care Center, Shop# 4C-035C, Level - 4, Block # C, Mobile Zone, Jamuna Future Park, Baridhara,Dhaka, Mobile: 01550150094 (9 AM to 5 PM)</p>
                        </div>
                        <button class="accordion">Gulshan</button>
                        <div class="panel">
                            <p>Gulshan Customer Care (BTCL Telephone Bhaban), BTCL Telephone Bhaban (Opposite of DCC Market), Gulshan-1, Dhaka 1212, Mobile: 01550157816 (9 AM to 5 PM)</p>
                        </div>
                        <button class="accordion">Kafrul</button>
                        <div class="panel">
                            <p>Kachukhet Customer Care, 1103,Ibrahimpur,Shop no:119, Ground Floor, Rupayan Nowfa Plaza,Kafrul Dhaka Cantonment,Dhaka-1206, Mobile: 01550157835 (9 AM to 5 PM)</p>
                        </div>
                        <button class="accordion">Shahjahanpur</button>
                        <div class="panel">
                            <p>Malibagh Customer Care, House # 92, Malibagh Shahid Faruk Taslim Road, (Opposite Sohag Bus Stand), 1st Floor, Malibagh, Dhaka-1217, Mobile: 01550157847 (9 AM to 5 PM)</p>
                        </div>
                        <button class="accordion">Sherebangla Nagar</button>
                        <div class="panel">
                            <p>Sher-e-Bangla Nagar Customer Care, Manik Mia Avenue, BTCL Compound, Opposite to Shangsad Bhaban, Sher-e-Bangla Nagar, Dhaka, Mobile: 01550157846 (9 AM to 5 PM)</p>
                        </div>
                        <button class="accordion">Rampura</button>
                        <div class="panel">
                            <p>Banasree Customer Care, House : 07, Block: B (Main Road), Ground Floor, Banasree, Rampura, Dhaka, Mobile: 01550157848 (9 AM to 5 PM)</p>
                        </div>
                        <button class="accordion">Uttara</button>
                        <div class="panel">
                            <p>Uttara (Post Office) Customer Care Center, Uttara Post Office, Road# 07, Sec# 03, Uttara Model Town, Dhaka-1230, Mobile: 01550157705 (9 AM to 5 PM)</p>
                        </div>
                        <button class="accordion">Gulshan</button>
                        <div class="panel">
                            <p>Gulshan-1 Project Office Care Center, House#39 , Road# 116 Gulshan-01 , Dhaka-1212, Mobile: 01550157850 (9 AM to 5 PM)</p>
                        </div>
                    </div>
                    <!-- Faridpur Zone -->
                    <button class="accordion">Faridpur</button>
                    <div class="panel">
                        <button class="accordion">Faridpur Sadar</button>
                        <div class="panel">
                            <p>Faridpur Teletalk Customer Care Center, Bahadur Market 14/111, Hazratala Mohalla, Faridpur Sadar, Goalchamat, Faridpur, Mobile: 01550150075 (9 AM to 5 PM)</p>
                        </div>
                    </div>
                    <!-- Gazipur Zone -->
                    <button class="accordion">Gazipur</button>
                    <div class="panel">
                        <button class="accordion">Gazipur Sadar-Joydebpur</button>
                        <div class="panel">
                            <p>Joydebpur Teletalk Customer Care Center, Noor plaza, Ground Floor, Joydebpur Chowrasta, Joydebpur, Gazipur, Mobile: 01550150023 (9 AM to 5 PM)</p>
                        </div>
                    </div>
                    <!-- Manikganj Zone -->
                    <button class="accordion">Manikganj</button>
                    <div class="panel">
                        <button class="accordion">Manikganj Sadar</button>
                        <div class="panel">
                            <p>Manikganj Teletalk Customer Care Center, House # 75, Anjali Super Market, Ground Floor, Manikgonj Sadar, Manikgonj, Mobile: 01550150028 (9 AM to 5 PM)</p>
                        </div>
                    </div>
                    <!-- Munshiganj Zone -->
                    <button class="accordion">Munshiganj</button>
                    <div class="panel">
                        <button class="accordion">Munshiganj Sadar</button>
                        <div class="panel">
                            <p>Munshiganj Teletalk Customer Care Center, 424 Jubili Road, Jagadhatri Para, Ward No.- 2,Khal East, Munshiganj Sadar, Munshiganj -1500, Mobile: 01550156760 (9 AM to 5 PM)</p>
                        </div>
                    </div>
                    <!-- Mymensingh Zone -->
                    <button class="accordion">Mymensingh</button>
                    <div class="panel">
                        <button class="accordion">Mymensingh Sadar</button>
                        <div class="panel">
                            <p>Mymensingh Teletalk Customer Care Center, 3, Ganginarpar (1st Floor) Mymensingh, Mobile: 01550150062 (9 AM to 5 PM)</p>
                        </div>
                    </div>
                    <!-- Narayanganj Zone -->
                    <button class="accordion">Narayanganj</button>
                    <div class="panel">
                        <button class="accordion">Narayanganj Sadar</button>
                        <div class="panel">
                            <p>Narayangonj Teletalk Customer Care Center, Sufia Plaza Ground Floor, 123 B.B. Road Chashara , Narayangonj, Mobile: 01550157786 (9 AM to 5 PM)</p>
                        </div>
                    </div>
                    <!-- Netrokona Zone -->
                    <button class="accordion">Netrokona</button>
                    <div class="panel">
                        <button class="accordion">Netrokona-S</button>
                        <div class="panel">
                            <p>Netrokona Customer Care Centre, 23, South Nagra, New Court Road (Opposite of Circuit House), Netrokona, Mobile: 01550157703 (9 AM to 5 PM)</p>
                        </div>
                    </div>
                    <!-- Tangail Zone -->
                    <button class="accordion">Tangail</button>
                    <div class="panel">
                        <button class="accordion">Tangail Sadar</button>
                        <div class="panel">
                            <p>Tangail Teletalk Customer Care Center, Kali Bari Road, 1st Floor, Shachin Mansion, Adalat Para, Tangail, Mobile: 01550150055 (9 AM to 5 PM)</p>
                        </div>
                    </div>
                    <!-- Bogra Zone -->
                    <button class="accordion">Bogra</button>
                    <div class="panel">
                        <button class="accordion">Bogra Sadar</button>
                        <div class="panel">
                            <p>Bogra Teletalk Customer Care Center, Islamic Studies Group Bhaban (1st Floor) Station Road, Satmatha, Bogra, Mobile: 01550150064 (9 AM to 5 PM)</p>
                        </div>
                    </div>
                    <!-- Joypurhat Zone -->
                    <button class="accordion">Joypurhat</button>
                    <div class="panel">
                        <button class="accordion">Joypurhat S</button>
                        <div class="panel">
                            <p>Joypurhat Teletalk Customer Care Center, Ansar ali complex, Ground floor,Sadar Main road, Joypurhat, Mobile: 01550157706 (9 AM to 5 PM)</p>
                        </div>
                    </div>
                    <!-- Naogaon Zone -->
                    <button class="accordion">Naogaon</button>
                    <div class="panel">
                        <button class="accordion">Naogaon Sadar</button>
                        <div class="panel">
                            <p>Naogoan Teletalk Customer Care Center, Rubir More, Naogoan Sadar, Naogaon, Mobile: 01550157829 (9 AM to 5 PM)</p>
                        </div>
                    </div>
                    <!-- Chapai Nawabganj Zone -->
                    <button class="accordion">Chapai Nawabganj</button>
                    <div class="panel">
                        <button class="accordion">Nawabganj Sadar</button>
                        <div class="panel">
                            <p>Chapai Nawabganj Teletalk Customer Care Center, 499 Baten Khar More, (In front of Islami Bank) Chapainawabgonj Sadar, Chapainawabgonj, Mobile: 01550157828 (9 AM to 5 PM)</p>
                        </div>
                    </div>
                    <!-- Pabna Zone -->
                    <button class="accordion">Pabna</button>
                    <div class="panel">
                        <button class="accordion">Pabna Sadar</button>
                        <div class="panel">
                            <p>Pabna Teletalk Customer Care Center, 2nd Floor, Gora Stand, Traffic Moor, Holding Number 41/0, Abdul Hamid Road, Pabna 6600, Mobile: 01550150070 (9 AM to 5 PM)</p>
                        </div>
                    </div>
                    <!-- Rajshahi Zone -->
                    <button class="accordion">Rajshahi</button>
                    <div class="panel">
                        <button class="accordion">Rajshahi</button>
                        <div class="panel">
                            <p>Rajshahi Teletalk Customer Experience Center, House # 356, Khan Bhaban (1st Floor) New Market (South Side), Station Road, Rajshahi, Mobile: 01550150063 (9 AM to 5 PM)</p>
                        </div>
                    </div>
                    <!-- Sirajgonj Zone -->
                    <button class="accordion">Sirajgonj</button>
                    <div class="panel">
                        <button class="accordion">Sirajgonj Sadar</button>
                        <div class="panel">
                            <p>Sirajgonj Teletalk Customer Care Center, Holding # 812, Ma-Mansion, 1st Floor, Station Road, Moktarpara Moor, Sirajganj, Mobile: 01550157817 (9 AM to 5 PM)</p>
                        </div>
                    </div>
                    <!-- Dinajpur Zone -->
                    <button class="accordion">Dinajpur</button>
                    <div class="panel">
                        <button class="accordion">Dinajpur Sadar</button>
                        <div class="panel">
                            <p>Dinajpur Teletalk Customer Care Center, Alam Corporation, (1st Floor), Jail Road, Munshipara, Dinajpur, Mobile: 01550157782 (9 AM to 5 PM)</p>
                        </div>
                    </div>
                    <!-- Kurigram Zone -->
                    <button class="accordion">Kurigram</button>
                    <div class="panel">
                        <button class="accordion">Kurigram Sadar</button>
                        <div class="panel">
                            <p>Kurigram Teletalk Customer Care Center, Troyee Nir, Holding No-0119-00, Ward No-05, Goshpara, Kurigram, Mobile: 01550157825 (9 AM to 5 PM)</p>
                        </div>
                    </div>
                    <!-- Rangpur Zone -->
                    <button class="accordion">Rangpur</button>
                    <div class="panel">
                        <button class="accordion">Rangpur Sadar</button>
                        <div class="panel">
                            <p>Rangpur Teletalk Customer Care Center, Rangpur Bhaban, Station Road Ground Floor, Rangpur, Mobile: 01550157787 (9 AM to 5 PM)</p>
                        </div>
                    </div>
                    <!-- Thakurgaon Zone -->
                    <button class="accordion">Thakurgaon</button>
                    <div class="panel">
                        <button class="accordion">Thakurgaon Sadar</button>
                        <div class="panel">
                            <p>Thakurgaon Teletalk Customer Care Center, M/S Gallery Traders, Zella School Gate, Bangabandhu Road, Thakurgaon, Mobile: 01550157818 (9 AM to 5 PM)</p>
                        </div>
                    </div>
                    <!-- Barisal Zone -->
                    <button class="accordion">Barisal</button>
                    <div class="panel">
                        <button class="accordion">Barisal Sadar</button>
                        <div class="panel">
                            <p>Barisal Teletalk Customer Care Center, Nurjahan Mansion (Ground floor), Bagura Road,Alekanda , Barisal-8200, Mobile: 01550150066 (9 AM to 5 PM)</p>
                        </div>
                    </div>
                    <!-- Bhola Zone -->
                    <button class="accordion">Bhola</button>
                    <div class="panel">
                        <button class="accordion">Bhola Sadar</button>
                        <div class="panel">
                            <p>Bhola Teletalk Customer Care Center , Azhar Mohal, Mohajon potti, Sadar Road, Bhola, Mobile: 01550156756 (9 AM to 5 PM)</p>
                        </div>
                    </div>
                    <!-- Jhalokati Zone -->
                    <button class="accordion">Jhalokati</button>
                    <div class="panel">
                        <button class="accordion">Jhalokati Sadar</button>
                        <div class="panel">
                            <p>Jhalokathi Teletalk Customer Care Center , 29, Ronalose Road, Kamar potti, Jhalokathi, Mobile: 01550157832 (9 AM to 5 PM)</p>
                        </div>
                    </div>
                    <!-- Bandarban Zone -->
                    <button class="accordion">Bandarban</button>
                    <div class="panel">
                        <button class="accordion">Bandarban Sadar</button>
                        <div class="panel">
                            <p>Bandarban Teletalk Customer Care Center , Nuel Plaza (1st floor), Jahangir Bhaban, K B Road, (In front of Bandarban Sadar Thana), Bandarban, Mobile: 01550150096 (9 AM to 5 PM)</p>
                        </div>
                    </div>
                    <!-- Chandpur Zone -->
                    <button class="accordion">Chandpur</button>
                    <div class="panel">
                        <button class="accordion">Chandpur Sadar</button>
                        <div class="panel">
                            <p>Chadpur Teletalk Customer Care Center, Sraboni Vella (Ground floor), Comilla Road, East side of Goni School, Chadpur, Mobile: 01550157826 (9 AM to 5 PM)</p>
                        </div>
                    </div>
                    <!-- Chittagong Zone -->
                    <button class="accordion">Chittagong</button>
                    <div class="panel">
                        <button class="accordion">Chittagong Sadar</button>
                        <div class="panel">
                            <p>Agrabad Teletalk Customer Experience Center , Ground Floor, BTCL Building, Sheikh Mujib Road, Agrabad, Chattogram, Mobile: 01550150078 (9 AM to 5 PM)</p>
                        </div>
                        <button class="accordion">Chittagong Sadar</button>
                        <div class="panel">
                            <p>Dampara Teletalk Customer Experience Center, Idris Center (1st floor) 444 M. M. Ali Road Dampara Chittagong, Mobile: 01550150079 (9 AM to 5 PM)</p>
                        </div>
                        <button class="accordion">Chittagong Sadar</button>
                        <div class="panel">
                            <p>Muradpur Customer Care Center, Ground Floor, BTCL Exchange Building, Muradpur, Chattogram, Mobile: 01550150026 (9 AM to 5 PM)</p>
                        </div>
                        <button class="accordion">Hathazari</button>
                        <div class="panel">
                            <p>Bandartila Customer Care Center, Sailors Colony, Opposite TCB Bhaban, Ground Floor, Bandartila, Chittagong, Mobile: 01550157867 (9 AM to 5 PM)</p>
                        </div>
                    </div>
                    <!-- Comilla Zone -->
                    <button class="accordion">Comilla</button>
                    <div class="panel">
                        <button class="accordion">Comilla Sadar</button>
                        <div class="panel">
                            <p>Comilla Teletalk Customer Care Center, 325/365 Jhawtola (1st Floor) Comilla, Mobile: 01550150068 (9 AM to 5 PM)</p>
                        </div>
                        <button class="accordion">Comilla Sadar</button>
                        <div class="panel">
                            <p>Mainamati Customer Care Center, Maynamati Sena Kollyan Market, Maynamoti, Comilla, Mobile: 01550157701 (9 AM to 5 PM)</p>
                        </div>
                    </div>
                    <!-- Cox's Bazar Zone -->
                    <button class="accordion">Cox's Bazar</button>
                    <div class="panel">
                        <button class="accordion">Cox's Bazar Sadar</button>
                        <div class="panel">
                            <p>Cox's Bazar Teletalk Customer Experience Center, BTCL Bhavan, Motel Road , Cox's Bazar, Mobile: 01550150059 (9 AM to 5 PM)</p>
                        </div>
                    </div>
                    <!-- Feni Zone -->
                    <button class="accordion">Feni</button>
                    <div class="panel">
                        <button class="accordion">Feni Sadar</button>
                        <div class="panel">
                            <p>Feni Customer Care Centre, Feni zilla Muktijudda complex,Feni zilla unit comand,Feni, Mobile: 01550157702 (9 AM to 5 PM)</p>
                        </div>
                    </div>
                    <!-- Khagrachari Zone -->
                    <button class="accordion">Khagrachari</button>
                    <div class="panel">
                        <button class="accordion">Khagrachhari</button>
                        <div class="panel">
                            <p>Khagrachori Teletalk Customer Care Center, Court Road, Diginala Road, Khagrachori, Mobile: 01550150081 (9 AM to 5 PM)</p>
                        </div>
                        <button class="accordion">Dighinala</button>
                        <div class="panel">
                            <p>Dighinala Customer Care Center, Plot-B, Nalanda Center(1st floor), Boalkhali Natun Bazar, Dighinala, Khagrachari, Mobile: 01550157704 (9 AM to 5 PM)</p>
                        </div>
                    </div>
                    <!-- Lakshmipur Zone -->
                    <button class="accordion">Lakshmipur</button>
                    <div class="panel">
                        <button class="accordion">Lakshmipur Sadar</button>
                        <div class="panel">
                            <p>Lakshmipur Teletalk Customer Care Center, Moddho Bancha Nagar, Holding No-1243, Ward No: 06, Laxmipur Sadar , Lakshmipur, Mobile: 01550157821 (9 AM to 5 PM)</p>
                        </div>
                    </div>
                    <!-- Noakhali Zone -->
                    <button class="accordion">Noakhali</button>
                    <div class="panel">
                        <button class="accordion">Noakhali Sadar</button>
                        <div class="panel">
                            <p>Noakhali Teletalk Customer Care Center, 768 Main Road, Maijdee Bazar, Maizdee ,Noakhali, Mobile: 01550156759 (9 AM to 5 PM)</p>
                        </div>
                    </div>
                    <!-- Rangamati Zone -->
                    <button class="accordion">Rangamati</button>
                    <div class="panel">
                        <button class="accordion">Rangamati Sadar</button>
                        <div class="panel">
                            <p>Rangamati Teletalk Customer Care Center, S.K. Market, 1st Floor Happier more, Bonorupa Rangamati, Mobile: 01550150095 (9 AM to 5 PM)</p>
                        </div>
                    </div>
                    <!-- Habiganj Zone -->
                    <button class="accordion">Habiganj</button>
                    <div class="panel">
                        <button class="accordion">Habiganj Sadar</button>
                        <div class="panel">
                            <p>Hobigonj Teletalk Customer Care Center, BTCL Compund, Hobigonj, Mobile: 01550157785 (9 AM to 5 PM)</p>
                        </div>
                    </div>
                    <!-- Sylhet Zone -->
                    <button class="accordion">Sylhet</button>
                    <div class="panel">
                        <button class="accordion">Sylhet Sadar</button>
                        <div class="panel">
                            <p>Sylhet Teletalk Customer Care Center, R. N. Tower (1st Floor) Chowhatta, Sylhet, Mobile: 01550150067 (9 AM to 5 PM)</p>
                        </div>
                    </div>
                    <!-- Bagerhat Zone -->
                    <button class="accordion">Bagerhat</button>
                    <div class="panel">
                        <button class="accordion">Bagerhat Sadar</button>
                        <div class="panel">
                            <p>Bagerhat Teletalk Customer Care Center, Press Club Bhaban (1st Floor),Shahid Minar Road Bagerhat, Mobile: 01550156755 (9 AM to 5 PM)</p>
                        </div>
                    </div>
                    <!-- Chuadanga Zone -->
                    <button class="accordion">Chuadanga</button>
                    <div class="panel">
                        <button class="accordion">Chuadanga-S</button>
                        <div class="panel">
                            <p>Chuadanga Teletalk Customer Care Center, Shohid Abul Kashem Road (Ground Floor), Infront of Town Football Math, Chuadanga-7200, Mobile: 01550157843 (9 AM to 5 PM)</p>
                        </div>
                    </div>
                    <!-- Jessore Zone -->
                    <button class="accordion">Jessore</button>
                    <div class="panel">
                        <button class="accordion">Jessore Sadar</button>
                        <div class="panel">
                            <p>Jessore Teletalk Customer Care Center, 4, BK Road, Bejpara, Jessore, Mobile: 01550157781 (9 AM to 5 PM)</p>
                        </div>
                    </div>
                    <!-- Jhenaidah Zone -->
                    <button class="accordion">Jhenaidah</button>
                    <div class="panel">
                        <button class="accordion">Jhenaidah Sadar</button>
                        <div class="panel">
                            <p>Jhenaidah Teletalk Customer Care Center, H.S.S. Road, Ground Floor, Kutum Community Center, Jhenaidah, Mobile: 01550157844 (9 AM to 5 PM)</p>
                        </div>
                    </div>
                    <!-- Khulna Zone -->
                    <button class="accordion">Khulna</button>
                    <div class="panel">
                        <button class="accordion">Khulna Metro</button>
                        <div class="panel">
                            <p>Khulna Teletalk Customer Experience Center, Teletalk Experience Center T.C.B Bhaban (1st Floor), 21-22 K.D.A Avenue C/A, Shibbari More, Khulna, Mobile: 01550150065 (9 AM to 5 PM)</p>
                        </div>
                        <button class="accordion">Phultala</button>
                        <div class="panel">
                            <p>Daulotpur Customer Care Center, Tanklory Bhabon, Ground Floor, Natun Raster moor, Kasipur, Daulotpur, Khulna, Mobile: 01550157868 (9 AM to 5 PM)</p>
                        </div>
                    </div>
                    <!-- Kushtia Zone -->
                    <button class="accordion">Kushtia</button>
                    <div class="panel">
                        <button class="accordion">Kushtia Sadar</button>
                        <div class="panel">
                            <p>Kushtia Teletalk Customer Care Center, Shatabdi Bhaban Market (2nd floor), 92, N.S Road, Thana Traffiq more, Kushtia, Mobile: 01550150097 (9 AM to 5 PM)</p>
                        </div>
                    </div>
                    <!-- Magura Zone -->
                    <button class="accordion">Magura</button>
                    <div class="panel">
                        <button class="accordion">Magura Sadar</button>
                        <div class="panel">
                            <p>Magura Teletalk Customer Care Center, 177, S.M. Plaza, M.R. Road, Magura, Mobile: 01550157833 (9 AM to 5 PM)</p>
                        </div>
                    </div>
                    <!-- Satkhira Zone -->
                    <button class="accordion">Satkhira</button>
                    <div class="panel">
                        <button class="accordion">Satkhira Sadar</button>
                        <div class="panel">
                            <p>Satkhira Teletalk Customer Care Center, 113, Shahid Kazol Saroni, Judge Court Road, Satkhira Sador, Satkhira -9400, Mobile: 01550157822 (9 AM to 5 PM)</p>
                        </div>
                    </div>
                </div>
























































            </div>

        </main>
        <footer>
            <?php
            Required::footer();
            ?>
        </footer>
    </div>


    <?php
    Required::jquery()->hamburgerMenu();
    ?>
    <script>
        var base_url = '<?php echo BASE_URL; ?>';
        $(function() {

            $(".panel p").each(function() {
                var text = $(this).text();
                console.log(text);
                text = text.replaceAll(",", "<br>");
                $(this).html(text);
            });

           
        })
    </script>

    <script>
        var acc = document.getElementsByClassName("accordion");
        var i;

        for (i = 0; i < acc.length; i++) {
            acc[i].addEventListener("click", function() {
                /* Toggle between adding and removing the "active" class,
            to highlight the button that controls the panel */
                this.classList.toggle("active");

                /* Toggle between hiding and showing the active panel */
                var panel = this.nextElementSibling;
                if (panel.style.display === "block") {
                    panel.style.display = "none";
                } else {
                    panel.style.display = "block";
                }
            });
        }
    </script>
</body>

</html>