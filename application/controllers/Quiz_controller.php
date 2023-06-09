<?php
defined('BASEPATH') OR exit('No direct script access allowed');

Class Quiz_controller extends CI_Controller{
    public function __construct(){
        parent:: __construct();
        $this->load->model('Quiz_model');
        if($this->session->userdata('name') == ""){
            redirect('User_controller/index');
        }
    }
    
    public function index(){
        $this->load->view('all_views/quiz_view');
    }

    public function play_quiz(){
        $start_time = date('Y-m-d H:i:s'); // Get the current time
        $this->session->set_userdata('start_time', $start_time); 
        $this->load->view('localstorage/localstorage_quiz');
    }

    // function to fetch question id, question, options and answer to send it to view
    public function fetch_data(){
        header("Access-Control-Allow-Origin: *");
        
        $ajax_id = $this->input->post();
        // $ajax_id = 1;
    
        if($ajax_id != NULL){

            $response = $this->Quiz_model->get_data($ajax_id['id']);
            $question = $response[0]->question;
    
            $answer;
            $id = $response[0]->q_id; // assuming all objects have the same q_id
            $options = array();
            
            foreach ($response as $i) {
    
                $options[] = $i->option;
                if ($i->answer == "1") {
                    $answer = $i->option;
                }
            }
    
            $data = array(
                'id' => $id,
                'question'=> $question,
                'options' => $options,
                'answer' => $answer
            );
            // var_dump($data);
            echo json_encode($data);
            
        }
        else{
            $response['status'] = "ERROR";
            $response['message'] = "EMPTY ID WAS SENT";
            echo json_encode($response);
        }
    }

    // Function to set data to database
    public function save_result(){
        header("Access-Control-Allow-Origin: *");
        $name = $this->session->userdata('name');
        $email = $this->session->userdata('email');
        
        
        $res = $this->Quiz_model->set_result();
        $res1 = $this->Quiz_model->save_preview($res);

        // var_dump($res1);
        if($res1){
            $response['status'] = "success";
            $response['message'] = "successfuly added data";
            echo json_encode($response);
        }
        else{
            $response['status'] = "error";
            $response['message'] = "error adding data";
            echo json_encode($response);
        }
    }


}
?>