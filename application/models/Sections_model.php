<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Sections_model extends CI_Model
{
    public function validate_data($section, $programSelect)
    {
        $this->db->where('section', $section);
        $this->db->where('program_id', $programSelect);
        $query = $this->db->get('tbl_sections');

        return $query->num_rows() > 0; // true if duplicate exists
    }
    public function insert_sections($data)
    {
        $this->db->insert('tbl_sections', $data);
        return $this->db->insert_id();
    }

    public function get_all_sections()
    {
        $this->db->select("
        s.id AS section_id,
        s.section,
        s.program_id,
        p.program_name,
    ");
        $this->db->from("tbl_sections s");
        $this->db->join("tbl_programs p", "p.id = s.program_id", "left");
        $this->db->order_by("s.section", "ASC");
        return $this->db->get()->result();
    }

       public function get_section($id) {
        $this->db->where('id', $id);
        $query = $this->db->get('tbl_sections');
        return $query->row_array();
    }

      public function update_section($id, $data) {
        $this->db->where('id', $id);
        return $this->db->update('tbl_sections', $data);
    }

    // Delete section by ID
    public function delete_section($id) {
        $this->db->where('id', $id);
        return $this->db->delete('tbl_sections');
    }

}