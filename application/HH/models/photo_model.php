<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Photo model handles everything related to the query
 * towards the photo table in the database
 */
class Photo_model extends CI_Model
{
    /**
     * id (PK, Auto Increment, int)
     * place_id (FK, Auto Increment, int)
     * photo_link (string)
     */

    private $table = 'photo';

    public function __construct()
    {
        parent::__construct();

        $this->load->database();
        log_msg(__CLASS__, __FUNCTION__, func_get_args());
    }

    /**
     * Query the images by place id
     *
     * @param int, place id
     * @return array of associative array of data
     */
    public function get_all($place_id = FALSE)
    {
        log_msg(__CLASS__, __FUNCTION__, func_get_args());
        if ($place_id !== FALSE)
        {   // else, get all data from table
            $this->db->where('place_id', $place_id);
        }

        $query = $this->db->get($this->table);

        return $query->result_array();
    }

    /**
     * Query the images of places and
     * return the array of associative array
     *
     * @param array of place data
     * @return array of associative array of data
     */
    public function get_by_places($place_array = NULL)
    {
        log_msg(__CLASS__, __FUNCTION__, func_get_args());
        if (empty($place_array))
        {
            return NULL;
        }

        // Loops through the place array to add to the where clause
        foreach($place_array as $place)
        {
            $this->db->or_where('id =', $place['id']);
        }

        $this->db->from($this->table);
        // take only 1 of each place
        $this->db->group_by('id');
        $query = $this->db->get();

        return $query->result_array();
    }

    /**
     * Adds a new image link to photos table
     *
     * @return bool, status of operation
     */
    public function add_link($photo_link = FALSE)
    {
        log_msg(__CLASS__, __FUNCTION__, func_get_args());
        $data = array();
        $data['place_id'] = $this->input->post('place_id');
        $data['photo_link'] = $photo_link;

        return $this->db->insert($this->table, $data);
    }

    /**
     * Deletes image by id
     *
     * NOTE: It doesn't delete image, only the link to it
     * TODO: Delete image from fileserver too?
     *
     * @param int, id
     * @return bool, status of operation
     */
    public function delete_photo($id = FALSE)
    {
        log_msg(__CLASS__, __FUNCTION__, func_get_args());
        if ($id === FALSE)
        {
            return FALSE;
        }

        $this->db->where('id', $id);
        $this->db->delete($this->table);

        return TRUE;
    }

    /**
     * Delete all images associated with a place
     *
     * @param int, place id
     * @return bool, status of operation
     */
    public function delete_photos_by_place($place_id = FALSE)
    {
        log_msg(__CLASS__, __FUNCTION__, func_get_args());
        if ($place_id === FALSE)
        {
            return FALSE;
        }

        $this->db->where('place_id', $place_id);
        $this->db->delete($this->table);

        return TRUE;
    }
}

/* End of file photos_model.php */
/* Location: application/models/photos_model.php */
