<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Place_model extends CI_Model
{
    /**
     * id (PK, Auto Increment, int)
     * category_id (FK, int)
     * area_id (FK, int)
     * name (string)
     * description (string)
     * address (string)
     * approved (bool)
     */

    private $table = 'place';

    public function __construct()
    {
        parent::__construct();

        $this->load->database();
        log_msg(__CLASS__, __FUNCTION__, func_get_args());
    }

    /**
     * Query for a place by it's id
     *
     * @param int, place id
     * @return associative array of data
     */
    public function get($id = FALSE)
    {
        log_msg(__CLASS__, __FUNCTION__, func_get_args());
        if ($id === FALSE)
        {
            return NULL;
        }

        $this->db->select('place.*, location.longitude, location.latitude, rating.total DIV rating.count AS rating, area.name AS area');
        $this->db->from($this->table);
        $this->db->join('rating', 'rating.place_id = place.id', 'left');
        $this->db->join('area', 'place.area_id = area.id');
        $this->db->join('location', 'location.place_id = place.id');
        $this->db->where('place.id', $id);
        $query = $this->db->get();

        $place = array();

        if ($query->num_rows() === 1)
            $place = $query->row_array();

        // get the extra details
        $this->db->from('place_detail');
        $this->db->where('place_id', $id);
        $extras = $this->db->get()->result_array();

        // get the column names to append to result
        $this->db->from('category_column');
        $this->db->where('category_id', $place['category_id']);
        $columns = $this->db->get()->result_array();

        // format result by appending extra details
        foreach($extras as $detail)
        {
            $col_name = $this->_get_column_name($detail['category_column_id'], $columns);
            $place[$col_name['column_name']] = $detail['detail'];
        }

        return $place;
    }

    /**
     * Returns category_name
     *
     * @param category_column_id
     * @param array of associative array of category_columns
     *
     * @return an associative array of category_column
     */
    private function _get_column_name($id, $arr)
    {
        foreach($arr as $row)
        {
            if ($row['id'] == $id)
            {
                return $row;
            }
        }
    }
    
    /**
     * Query all the places
     *
     * @return array of associative array of data
     */
    public function get_all()
    {
        log_msg(__CLASS__, __FUNCTION__, func_get_args());
        $query = $this->db->get($this->table);
        return $query->result_array();
    }


    /**
     * Query all the places
     *
     * @return array of associative array of data
     */
    public function get_all_sorted()
    {
        log_msg(__CLASS__, __FUNCTION__, func_get_args());
        $this->db->from($this->table);
        $this->db->order_by('name');
        $query = $this->db->get();
        return $query->result_array();
    }

    /**
     * Query all the places by category id
     * Used on the listing places page
     *
     * Keys: (id, name, approved, rating, area)
     *
     * @param int, category id
     * @return array of associative array of data
     */
    public function get_by_category($category_id = FALSE)
    {
        log_msg(__CLASS__, __FUNCTION__, func_get_args());
        if ($category_id === FALSE)
        {
            return NULL;
        }

        $this->db->select('place.id, place.name, place.approved,
            rating.total DIV rating.count AS rating, area.name AS area');
        $this->db->from($this->table);
        $this->db->join('rating', 'rating.place_id = place.id');
        $this->db->join('area', 'area.id = place.area_id');
        $this->db->where('place.category_id', $category_id);
        $query = $this->db->get();
        return $query->result_array();
    }

    /**
     * Query all the places by area id
     *
     * Keys: (id, name, approved, rating, area)
     *
     * @param int, area id
     * @return array of associative array of data
     */
    public function get_by_area($area_id = FALSE)
    {
        log_msg(__CLASS__, __FUNCTION__, func_get_args());
        if ($area_id === FALSE)
        {
            return NULL;
        }

        $this->db->select('place.id, place.name, place.approved,
            rating.total DIV rating.count AS rating, area.name AS area');
        $this->db->from($this->table);
        $this->db->join('rating', 'rating.place_id = place.id');
        $this->db->join('area', 'area.id = place.area_id');
        $this->db->where('area_id', $area_id);
        $query = $this->db->get();
        return $query->result_array();
    }
    
    /**
     * Query all the places by area id and category id
     *
     * Keys: (id, name, approved, rating, area)
     *
     * @param int, area id
     * @param int, category id
     * @return array of associative array of data
     */
    public function get_by_area_category($area_id = FALSE, $category_id = FALSE)
    {
        log_msg(__CLASS__, __FUNCTION__, func_get_args());
        if ($area_id === FALSE || $category_id === FALSE)
        {
            return NULL;
        }

        $this->db->select('place.id, place.name, place.approved,
            rating.total DIV rating.count AS rating, area.name AS area');
        $this->db->from($this->table);
        $this->db->join('rating', 'rating.place_id = place.id');
        $this->db->join('area', 'area.id = place.area_id');
        $this->db->where('area_id', $area_id);
        $this->db->where('category_id', $category_id);
        $query = $this->db->get();
        return $query->result_array();
    }

    /**
     * Query places by ids
     *
     * @param array of int
     * @return array of associative array of data
     */
    public function get_by_ids($array = NULL)
    {
        log_msg(__CLASS__, __FUNCTION__, func_get_args());
        if (empty($array))
        {
            return NULL;
        }

        for ($i = 0; $i < count($array); $i++)
        {
            $this->db->or_where('id =', $array[$i]);
        }

        $query = $this->db->get($this->table);
        return $query->result_array();
    }

    /**
     * Adds a new place to the db
     *
     * @return bool, status of operation
     */
    public function add_place()
    {
        log_msg(__CLASS__, __FUNCTION__, func_get_args());
        
        $data = array();
        $data['category_id'] = $this->input->post('place_category_id');
        $data['area_id'] = $this->input->post('place_area_id');
        $data['name'] = $this->input->post('place_name');
        $data['description'] = $this->input->post('place_description');
        $data['address'] = $this->input->post('place_address');

        // By default, not approved and rating 0
        $data['approved'] = FALSE;

        if (empty($data['name']) OR empty($data['description']))
        {
            return FALSE;
        }

        // insert to place table
        $this->db->insert($this->table, $data);

        // get id of previous entry
        $data['id'] = $this->db->insert_id();

        // initialize rating to 0
        $rating['total'] = 0;
        $rating['count'] = 0;
        $rating['place_id'] = $data['id'];

        $this->db->insert('rating', $rating);

        // add location to db
        $location['place_id'] = $data['id'];
        $location['latitude'] = $this->input->post('place_latitude');
        $location['longitude'] = $this->input->post('place_longitude');

        $this->db->insert('location', $location);

        // get extra columns
        $this->db->from('category_column');
        $this->db->where('category_id', $data['category_id']);
        $query = $this->db->get()->result_array();

        // insert data to extra columns
        $batch = array();
        foreach ($query as $row)
        {
            $batch[] = array(
                'place_id' => $data['id'],
                'category_column_id' => $row['id'],
                'detail' => $this->input->post('column_'.$row['id'])
            );
        }

        $this->db->insert_batch('place_detail', $batch);
        
        return $data['id'];
    }

    /**
     * Modify place details in db
     *
     * @return bool, status of operation
     */
    public function modify_place($place_id = FALSE)
    {
        log_msg(__CLASS__, __FUNCTION__, func_get_args());
        
        if ($place_id === FALSE)
        {
            return FALSE;
        }
        $data = array();
        $data['id'] = $place_id;
        $data['category_id'] = $this->input->post('place_category_id');
        $data['area_id'] = $this->input->post('place_area_id');
        $data['name'] = $this->input->post('place_name');
        $data['description'] = $this->input->post('place_description');
        $data['address'] = $this->input->post('place_address');

        $this->db->where('id', $data['id']);
        $this->db->update($this->table, $data); 
        
        // delete old details
        $this->db->where('place_detail.place_id', $data['id']);
        $this->db->delete('place_detail');
        
        // get extra columns
        $this->db->from('category_column');
        $this->db->where('category_id', $data['category_id']);
        $query = $this->db->get()->result_array();
        
        // insert data to extra columns
        $batch = array();
        foreach ($query as $row)
        {
            $batch[] = array(
                'place_id' => $data['id'],
                'category_column_id' => $row['id'],
                'detail' => $this->input->post('column_'.$row['id'])
            );
        }

        return $this->db->insert_batch('place_detail', $batch);
    }

    /**
     * Deletes a place by id
     * TODO: Also delete in the place_details table
     *
     * @param int, place id
     * @return bool, status of operation
     */
    public function remove_place($id = FALSE)
    {
        log_msg(__CLASS__, __FUNCTION__, func_get_args());
        if ($id === FALSE)
        {
            return FALSE;
        }

        $this->db->where('id', $id);
        $this->db->delete($this->table);

        // delete details
        $this->db->where('place_detail.place_id', $id);
        $this->db->delete('place_detail');
        
        // delete location
        $this->db->where('location.place_id', $id);
        $this->db->delete('location');
        
        // delete photos
        $this->db->where('photo.place_id', $id);
        $this->db->delete('photo');
        
        // delete rating
        $this->db->where('rating.place_id', $id);
        $this->db->delete('rating');
        return TRUE;
    }
}

/* End of file place_model.php */
/* Location: application/models/place_model.php */
