<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Category_model extends CI_Model
{
    public $id = '';
    public $table_name = '';
    public $display_name = '';
    public $subtype = '';

    private $table = 'categories';

    public function __construct()
    {
        $this->load->database();
    }

    /**
     * Get category by id
     *
     * @param int, category id
     * @return associative array of data
     */
    public function get($id = FALSE)
    {
        if ($id === FALSE)
        {
            return NULL;
        }

        $this->db->where('id', $id);
        $query = $this->db->get($this->table);

        if ($query->num_rows() === 1)
            return $query->row_array();
    }

    /**
     * Query all the categories
     *
     * @return array of associative array of data
     */
    public function get_all()
    {
        $query = $this->db->get($this->table);
        return $query->result_array();
    }

    /**
     * Add a new category
     *
     * @return bool, status of operation
     **/
    public function add()
    {
        $data = array();
        $data['table_name'] = $this->input->post('table_name');
        $data['display'] = $this->input->post('display_name');
        $data['subtype'] = FALSE;

        return $this->db->insert($this->table, $data);
    }

    /**
     * Deletes a category by id
     *
     * @param int, category id
     * @return bool, status of operation
     */
    public function remove($id = FALSE)
    {
        if ($id === FALSE)
        {
            return FALSE;
        }

        $this->db->where('id', $id);
        $this->db->delete($this->table);

        return TRUE;
    }
}

/* End of file category_model.php */
/* Location: application/models/category_model.php */