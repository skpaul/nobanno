<?php
 class AdminLeftNav{

     /**
     * prepare()
     * 
     * This method has dynamic argument(s).
     * 
     * Arguments- (1)string $role (2)string $baseUrl
     */

    public static function CreateFor(string $role, string $baseUrl, string $session_id = ""){
        $numberOfArguments = func_num_args();
        $arguments = func_get_args();
        $role = $arguments[0]; 
        $baseUrl = $arguments[1]; 
        $leftNav = "";
        if(strtolower($role) == "superadmin"){
            $leftNav = <<<HTML
                <ul>
                    <li><a href="$baseUrl/admins/dashboard.php?session-id=$session_id"><span class="m-icons">dashboard</span>Dashboard</a></li>
                    <li><a href="#"><span class="m-icons">add_task</span>Exam Session</a>
                        <ul>
                            <li><a href="$baseUrl/admins/exam-session/create/create-new-exam-session.php?session-id=$session_id"><span class="m-icons">dashboard</span>Create New</a></li>
                            <li><a href="$baseUrl/admins/exam-session/read/list/exam-session-list.php?session-id=$session_id"><span class="m-icons">format_list_numbered</span>List</a></li>
                        </ul>
                    </li>

                    <li><a href="#"><span class="m-icons">auto_stories</span>Registration</a>
                        <ul>
                            <li>
                                <a href="#"><span class="m-icons">post_add</span>New</a>
                                <ul>
                                    <li><a href="$baseUrl/admins/registration/create/upload-csv/upload-registration-csv.php?session-id=$session_id"><span class="m-icons">backup</span> Multiple (CSV)</a></li>
                                    <li><a href="$baseUrl/admins/registration/create/single/create-single-registration.php?session-id=$session_id"><span class="m-icons">post_add</span>Single</a></li>
                                </ul>
                            </li>
                           
                            <li>
                                <a href="$baseUrl/admins/registration/read/list/registration-list.php?session-id=$session_id"><span class="m-icons">format_list_numbered</span>List</a>
                            </li>
                        </ul>
                    </li>

                    <li><a href="#"><span class="m-icons">auto_stories</span>Correction Requests</a>
                        <ul>
                            <li>
                                <a href="$baseUrl/admins/registration-update-request/read/list/registration-update-request-list.php?session-id=$session_id"><span class="m-icons">format_list_numbered</span>List</a>
                            </li>
                        </ul>
                    </li>

                    <li>
                        <a href="#"><span class="m-icons">newspaper</span>Application</a>
                        <ul>
                            <li>
                                <a href="$baseUrl/admins/application/read/list/application-list.php?session-id=$session_id"><span class="m-icons">format_list_numbered</span>List</a>
                            </li>
                        </ul>
                    </li>
                    <li>
                        <a href="#"><span class="m-icons">contact_page</span>Admit Card</a>
                    </li>
                    <li>
                        <a href="#"><span class="m-icons">airline_seat_recline_normal</span>Seat Plan</a>
                        <ul>
                        <li>
                            <a href="#"><span class="m-icons">post_add</span>New</a>
                            <ul>
                                <li><a href="$baseUrl/admins/seat-plan/create/upload-csv/upload-seat-plan-csv.php?session-id=$session_id"><span class="m-icons">backup</span> Multiple (CSV)</a></li>
                                <li><a href="$baseUrl/admins/seat-plan/create/single/create-seat-plan.php?session-id=$session_id"><span class="m-icons">summarize</span>Single</a></li>
                            </ul>
                            </li>
                            <li><a href="$baseUrl/admins/seat-plan/read/list/seat-plan-list.php?session-id=$session_id"><span class="m-icons">summarize</span>List</a></li>
                        </ul>
                    </li>
                    <li>
                        <a href="#"><span class="m-icons">campaign</span>Notices</a>
                        <ul>
                            <li><a href="$baseUrl/admins/notice/create/create-notice.php?session-id=$session_id"><span class="m-icons">add_circle_outline</span>New</a></li>
                            <li><a href="$baseUrl/admins/notice/read/list/notice-list.php?session-id=$session_id"><span class="m-icons">format_list_numbered</span>List</a></li>
                        </ul>
                    </li>

                    <li>
                        <a href="$baseUrl/admins/logout.php?session-id=$session_id"><span class="m-icons">logout</span>Logout</a>
                    </li>
                </ul>
            HTML;
        }

        #region "registration" role
            if(strtolower($role) == "reg"){
                $leftNav = <<<HTML
                    <ul>
                        <li><a href="$baseUrl/admins/dashboard.php?session-id=$session_id"><span class="m-icons">dashboard</span>Dashboard</a></li>

                        <li><a href="#"><span class="m-icons">auto_stories</span>Registration</a>
                            <ul>
                                <li>
                                    <a href="#"><span class="m-icons">post_add</span>New</a>
                                    <ul>
                                        <!-- <li><a href="$baseUrl/admins/registration/create/upload-csv/upload-registration-csv.php?session-id=$session_id"><span class="m-icons">backup</span> Multiple (CSV)</a></li> -->
                                        <li><a href="$baseUrl/admins/registration/create/single/create-single-registration.php?session-id=$session_id"><span class="m-icons">post_add</span>Single</a></li>
                                    </ul>
                                </li>
                            
                                <li>
                                    <a href="$baseUrl/admins/registration/read/list/registration-list.php?session-id=$session_id"><span class="m-icons">format_list_numbered</span>List</a>
                                </li>
                            </ul>
                        </li>

                        <li><a href="#"><span class="m-icons">auto_stories</span>Correction Requests</a>
                            <ul>
                                <li>
                                    <a href="$baseUrl/admins/registration-update-request/read/list/registration-update-request-list.php?session-id=$session_id"><span class="m-icons">format_list_numbered</span>List</a>
                                </li>
                            </ul>
                        </li>

                        <li>
                            <a href="$baseUrl/admins/logout.php?session-id=$session_id"><span class="m-icons">logout</span>Logout</a>
                        </li>
                    </ul>
                HTML;
            }
        #endregion

        if(strtolower($role) == "exam"){
            $leftNav = <<<HTML
                <ul>
                    <li><a href="$baseUrl/admins/dashboard.php?session-id=$session_id"><span class="m-icons">dashboard</span>Dashboard</a></li>

                    <li>
                        <a href="#"><span class="m-icons">auto_stories</span>Registration</a>
                        <ul>
                            <li>
                                <a href="$baseUrl/admins/registration/read/list/registration-list.php?session-id=$session_id&ro=NnU2R0hS"><span class="m-icons">format_list_numbered</span>List</a>
                            </li>
                        </ul>
                    </li>

                    <li>
                        <a href="#"><span class="m-icons">newspaper</span>Application</a>
                        <ul>
                            <li>
                                <a href="$baseUrl/admins/application/read/list/application-list.php?session-id=$session_id"><span class="m-icons">format_list_numbered</span>List</a>
                            </li>
                        </ul>
                    </li>
                    
                    <li>
                            <a href="$baseUrl/admins/logout.php?session-id=$session_id"><span class="m-icons">logout</span>Logout</a>
                        </li>
                </ul>
            HTML;
        }

        if(strtolower($role) == "other"){
            $leftNav = <<<HTML
                <ul>
                    <li><a href="$baseUrl/admins/dashboard.php"><span class="m-icons">dashboard</span>Dashboard</a></li>
                    <li><a href="#"><span class="m-icons">add_task</span>Session</a></li>

                    <li><a href="#"><span class="m-icons">list_alt</span>Registration</a>
                        <ul>
                            <li><a href="$baseUrl/admins/registrations/new-registration/upload-csv/upload-csv.php"><span class="m-icons">backup</span>Upload CSV</a></li>
                        </ul>
                    </li>
                </ul>
            HTML;
        }

        return $leftNav;
    }
}
?>


