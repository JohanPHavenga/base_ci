<?php

class Login extends MY_Controller {

    public function __construct() {
        parent::__construct();
    }

    public function logout($confirm = false) {
        if ($confirm != "confirm") {
            $this->session->unset_userdata('user');
            redirect("/logout/confirm");
        } else {
            $this->load->view($this->header_url, $this->data_to_views);
            $this->load->view('login/logout', $this->data_to_views);
            $this->load->view($this->footer_url, $this->data_to_views);
        }
    }

    public function destroy($confirm = false) {
        // testing function
        $this->session->sess_destroy();
        delete_cookie("session_token");
        redirect("/logout/confirm");
    }

    public function userlogin() {
        $this->load->model('user_model');
        $this->load->model('role_model');
        $this->load->model('history_model');
        $this->data_to_views['page_title'] = "User Login";
        $this->data_to_views['form_url'] = '/login/userlogin/submit';
        $this->data_to_views['error_url'] = '/login/userlogin';
        $this->data_to_views['success_url'] = '/';

        // validation rules
        $this->form_validation->set_rules('user_email', 'Email', 'required', ["required" => "Enter your email address to log in"]);
        $this->form_validation->set_rules('user_password', 'Password', 'required', ["required" => "Please enter your password"]);

        // load correct view
        if ($this->form_validation->run() === FALSE) {
            $this->load->view($this->header_url, $this->data_to_views);
            $this->load->view('login/userlogin', $this->data_to_views);
            $this->load->view($this->footer_url, $this->data_to_views);
        } else {
            // check die login credentials. If fail, give nice error message
            $check_login = $this->user_model->check_credentials($this->input->post('user_email'), $this->input->post('user_password'));
            if ($check_login) {
                // check of email address confirmed is. Anders, gee nice error message
                $is_confirmed = $this->user_model->check_user_is_confirmed($check_login['user_id']);
                if ($is_confirmed) {
                    $this->session->set_userdata("user", $check_login);
                    $_SESSION['user']['logged_in'] = true;
                    $_SESSION['user']['role_list'] = $this->role_model->get_role_list_per_user($check_login['user_id']);
                    $this->session->set_flashdata([
                        'alert' => "Login successfull",
                        'status' => "success",
                    ]);
                    $history_data = ["user_id" => $check_login['user_id']];
                    $this->history_model->update_history_field($history_data, get_cookie('session_token'));
                    redirect($this->data_to_views['success_url']);
                } else {
                    $this->session->set_flashdata([
                        'alert' => "Seems your email address has not been confirmed yet. Please click here to resend confirmation email",
                        'status' => "warning",
                    ]);

                    redirect($this->data_to_views['error_url']);
                }
            } else {
                $this->session->set_flashdata([
                    'alert' => "Sorry, either your username or password was incorrect. Please try again",
                    'status' => "warning",
                ]);

                redirect($this->data_to_views['error_url']);
            }
            die("Login failure");
        }
    }

}
