<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Place extends CI_Controller {

    private $data;

    public function __construct()
    {
        parent::__construct();
        log_msg(__CLASS__, __FUNCTION__, func_get_args());
        $this->data['href'] = $this->config->item('href');

        $this->load->library('session');

        // verify login status
        if ( ! $this->session->userdata('logged_in')) redirect($this->data['href']['admin']['login']);
    }

    /* *
     * Shows the main page
     * */
    public function index()
    {
        log_msg(__CLASS__, __FUNCTION__, func_get_args());
        $this->read();
    }

    /* *
     * TODO:
     * Create a new place and insert it into the database
     * */
    public function create()
    {
        log_msg(__CLASS__, __FUNCTION__, func_get_args());

        $this->load->helper('form');

        if($this->input->post('submit'))
        {
            // Load data from form & modify place details
            // then load the place detail page
            $this->load->model('place_model');
            $new_place_id = $this->place_model->add_place();

            // view updated page
            $this->load->helper('url');
            redirect($this->data['href']['admin']['details'].'/'.$new_place_id, 'refresh');
        }
        else
        {
            // Required models
            $this->load->model('place_model');
            $this->load->model('area_model');
            $this->load->model('category_model');
            $this->load->model('column_model');

            // Data required
            $this->data['areas'] = $this->area_model->get_all();
            $this->data['categories'] = $this->category_model->get_all();
            $this->data['columns'] = $this->column_model->get_all();

            // Load the view for user to modify details
            // Load the view
            $this->data['title'] = 'Create New Place';
            $this->data['head'] = $this->load->view('templates/head', $this->data, TRUE);
            $this->data['banner'] = $this->load->view('templates/banner', $this->data, TRUE);
            $this->data['navbar'] = $this->load->view('templates/navbar', $this->data, TRUE);
            $this->data['js'] = $this->load->view('templates/js', $this->data, TRUE);

            $this->load->view('admin/new', $this->data);
        }
    }

    /* *
     * Loads the page to view all places
     * */
    public function read()
    {
        log_msg(__CLASS__, __FUNCTION__, func_get_args());

        $this->load->helper('form');

        $this->load->model('place_model');
        $this->load->model('search_model');

        $search = $this->input->post('search');

        $places_search = [];
        
        if ($search != "")
            $places_search = $this->search_model->find_all_sorted($search);

        // Check if there are any results
        if (sizeof($places_search) == 0)
        {
            $this->data['places'] = $this->place_model->get_all_sorted();
        }
        else
        {
            foreach ($places_search as &$place)
            {
                $this->data['places'][] = $this->place_model->get($place);
            }
        }

        // Load the view
        $this->data['title'] = 'All places';
        $this->data['head'] = $this->load->view('templates/head', $this->data, TRUE);
        $this->data['banner'] = $this->load->view('templates/banner', $this->data, TRUE);
        $this->data['navbar'] = $this->load->view('templates/navbar', $this->data, TRUE);
        $this->data['js'] = $this->load->view('templates/js', $this->data, TRUE);

        $this->load->view('admin/view', $this->data);
    }

    /* *
     * TODO:
     * Edit details of a place
     *
     * @param int place_id
     * */
    public function update($place_id = FALSE)
    {
        log_msg(__CLASS__, __FUNCTION__, func_get_args());

        if ($place_id === FALSE)
        {
            show_404();
        }

        $this->load->helper('form');

        if($this->input->post('submit'))
        {
            // Load data from form & modify place details
            // then load the place detail page
            $this->load->model('place_model');
            $this->place_model->modify_place($place_id);

            // view updated page
            $this->load->helper('url');
            redirect($this->data['href']['admin']['details'].'/'.$place_id, 'refresh');
        }
        else
        {
            // Required models
            $this->load->model('place_model');
            $this->load->model('area_model');
            $this->load->model('category_model');
            $this->load->model('column_model');

            // Get the desired place
            $this->data['place'] = $this->place_model->get($place_id);

            // Data required
            $this->data['areas'] = $this->area_model->get_all();
            $this->data['categories'] = $this->category_model->get_all();
            $this->data['columns'] = $this->column_model->get_all();

            // Load the view for user to modify details
            // Load the view
            $this->data['title'] = 'Modify '.$this->data['place']['name'];
            $this->data['head'] = $this->load->view('templates/head', $this->data, TRUE);
            $this->data['banner'] = $this->load->view('templates/banner', $this->data, TRUE);
            $this->data['navbar'] = $this->load->view('templates/navbar', $this->data, TRUE);
            $this->data['js'] = $this->load->view('templates/js', $this->data, TRUE);

            $this->load->view('admin/edit', $this->data);
        }
    }

    /* *
     * Removes a certain place
     *
     * @param int place_id
     * */
    public function remove($place_id = FALSE)
    {
        log_msg(__CLASS__, __FUNCTION__, func_get_args());

        if ($place_id === FALSE)
        {
            show_404();
        }

        $this->load->model('place_model');
        $this->place_model->remove_place($place_id);

        // Reload places page after a place is removed
        $this->read();
    }

    /* *
     * Removes a certain place
     *
     * @param int place_id
     * */
    public function photo($place_id = FALSE)
    {
        log_msg(__CLASS__, __FUNCTION__, func_get_args());

        if ($place_id === FALSE)
        {
            show_404();
        }

        $this->load->model('photo_model');
        $this->load->model('place_model');
        $this->load->model('category_model');

        $this->load->helper(array('form', 'url'));

        // get hidden data
        $place = $this->place_model->get($place_id);
        $category = $this->category_model->get($place['category_id']);

        $this->data['place_id'] = $place_id;
        $this->data['category_name'] = url_title($category['name'], '_', TRUE);
        $this->data['photos'] = $this->photo_model->get_all($place_id);

        // Load the view for user to modify details
        // Load the view
        $this->data['title'] = 'Edit Photos';
        $this->data['head'] = $this->load->view('templates/head', $this->data, TRUE);
        $this->data['banner'] = $this->load->view('templates/banner', $this->data, TRUE);
        $this->data['navbar'] = $this->load->view('templates/navbar', $this->data, TRUE);
        $this->data['js'] = $this->load->view('templates/js', $this->data, TRUE);

        $this->load->view('admin/photo', $this->data);
    }

    public function pop_photo($place_id = FALSE, $photo_id = FALSE)
    {
        log_msg(__CLASS__, __FUNCTION__, func_get_args());
        if ($place_id === FALSE OR $photo_id === FALSE)
        {
            show_404();
        }

        $this->load->model('photo_model');
        $this->photo_model->delete_photo($photo_id);
        $this->photo($place_id);
    }

    public function thumbnail($place_id = FALSE)
    {
        log_msg(__CLASS__, __FUNCTION__, func_get_args());

        if ($place_id === FALSE)
        {
            show_404();
        }

        $this->load->model('place_model');

        $this->load->helper(array('form', 'url'));

        // get hidden data
        $place = $this->place_model->get($place_id);

        $this->data['place'] = $place;
        $this->data['place_name'] = $place['name'];

        // Load the view for user to modify details
        // Load the view
        $this->data['title'] = 'Edit Photos';
        $this->data['head'] = $this->load->view('templates/head', $this->data, TRUE);
        $this->data['banner'] = $this->load->view('templates/banner', $this->data, TRUE);
        $this->data['navbar'] = $this->load->view('templates/navbar', $this->data, TRUE);
        $this->data['js'] = $this->load->view('templates/js', $this->data, TRUE);

        $this->load->view('admin/thumb', $this->data);
    }

    public function upload_photo()
    {
        log_msg(__CLASS__, __FUNCTION__, func_get_args());

        // get hidden values
        $place_id = $this->input->post('place_id');
        $category_name = $this->input->post('category_name');
        $upload_path = './public/images/places/'.$category_name.'/';

        // upload config
        $config['upload_path'] = $upload_path;
        $condig['overwrite'] = True;
        $config['allowed_types'] = 'jpg|png';
        $config['max_size']	= '900KB';
        $config['max_width']  = '960';
        $config['max_height']  = '960';

        $this->load->library('upload', $config);

        if ( ! $this->upload->do_upload())
        {
            // get and display error returned
            $error = array('error' => $this->upload->display_errors());
            $data = array('upload_data' => $this->upload->data());
            $output_error = $error['error'].'Extra details:<br />'.json_encode($data['upload_data']);
            show_error($output_error);
        }
        else
        {
            $data = array('upload_data' => $this->upload->data());
            $file_name = $data['upload_data']['file_name'];

            // update database
            $this->load->model('photo_model');
            $this->photo_model->add_link($upload_path.$file_name);

            // return to page view
            $this->photo($place_id);
        }
    }

    public function upload_thumbnail()
    {
        log_msg(__CLASS__, __FUNCTION__, func_get_args());

        // get hidden values
        $place_path = $this->input->post('place_path');
        $upload_path = './public/images/places/thumbnails/';

        // upload config
        $config['upload_path'] = $upload_path;
        $config['file_name'] = $place_path;
        $condig['overwrite'] = True;
        $config['allowed_types'] = 'jpg|png';
        $config['max_size']	= '100KB';
        $config['max_width']  = '128';
        $config['max_height']  = '96';

        $this->load->library('upload', $config);

        if ( ! $this->upload->do_upload())
        {
            // get and display error returned
            $error = array('error' => $this->upload->display_errors());
            $data = array('upload_data' => $this->upload->data());
            $output_error = $error['error'].'Extra details:<br />'.json_encode($data['upload_data']);
            show_error($output_error);
        }
        else
        {
            $data = array('upload_data' => $this->upload->data());
            $file_name = $data['upload_data']['file_name'];

            // return to page view
            $this->read();
        }
    }
    
    /**
     * Display the admin specific details of a place
     *
     * @param int , place id
     */
    public function details($place_id = FALSE)
    {
        log_msg(__CLASS__, __FUNCTION__, func_get_args());
        // change rating if there's any post method call
        if ($this->input->post()) {
            if ($this->input->cookie('hh_place_'.$place_id)) {
                $this->output->set_status_header('200');
                return;
            }

            $this->_update_rating();

            $cookie = array(
                'name'   => 'place_'.$place_id,
                'value'  => 'set',
                'expire' => '2628000', // 1 month in seconds
                'path'   => '/',
            );

            $this->input->set_cookie($cookie);
            $this->output->set_status_header('204');
            return;
        }

        //helpers
        $this->load->helper('text');
        $this->load->helper('form');

        if ($place_id === FALSE)
        {
            show_404();
        }

        $this->load->model('place_model', 'place');
        $this->load->model('category_model', 'category');
        $this->load->model('photo_model', 'photo');

        // get data from db
        $this->data['place'] = $this->place->get($place_id);

        if (empty($this->data['place']))
        {
            show_404();
        }

        $this->data['place']['photos'] = $this->photo->get_all($place_id);
        if (! empty($this->data['place']['website']))
        {
            $this->data['place']['website'] = prep_url($this->data['place']['website']);
        }
        $this->data['photos_result'] = sizeof($this->data['place']['photos']) == 0 ? 0 : 1;

        $category = $this->category->get($this->data['place']['category_id']);

        // load views
        $this->data['title'] = $category['name'];
        $this->data['head'] = $this->load->view('templates/head', $this->data, TRUE);
        $this->data['banner'] = $this->load->view('templates/banner', $this->data, TRUE);
        $this->data['navbar'] = $this->load->view('admin/templates/navbar', $this->data, TRUE);
        $this->data['js'] = $this->load->view('templates/js', $this->data, TRUE);
        $this->load->view(url_title($category['name'], '_', TRUE).'/detail', $this->data);
    }
}

/* End of file place.php */
/* Location: ./application/controllers/admin/place.php */
