<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Test extends CI_Controller {

    public function index()
    {
        $area="Dhaka";

        echo '1='. isset($area); //returns true which is OK

        echo '<br>2='. isset($area['division']); //returns true why?

// actually, any array key of area returns true

        echo '<br>3='. isset($area['ANY_KEY']);//this is my question 1

    }
}
