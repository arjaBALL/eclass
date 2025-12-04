<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Program_model extends CI_Model
{
    public function validate_data($program,$programDetails, $departmentSelect, $id = null)
    {
        $this->db->where('program_name', $program);
         $this->db->where('program_name', $programDetails);
        $this->db->where('department_id', $departmentSelect);
        if($id) {
            $this->db->where('id !=', $id); // ignore current ID for edit
        }
        $query = $this->db->get('tbl_programs');

        return $query->num_rows() > 0; // true if duplicate exists
    }

    public function insert_programs($data)
    {
        $this->db->insert('tbl_programs', $data);
        return $this->db->insert_id();
    }

    public function update_program($id, $data)
    {
        $this->db->where('id', $id);
        return $this->db->update('tbl_programs', $data);
    }

    public function delete_program($id)
    {
        $this->db->where('id', $id);
        return $this->db->delete('tbl_programs');
    }

    public function get_all_programs()
    {
        $this->db->select("p.id, p.program_name, p.program_details, d.id, d.department");
        $this->db->from("tbl_programs p");
        $this->db->join("tbl_departments d", "d.id = p.department_id", "left");
        $this->db->order_by("p.program_name", "ASC");
        return $this->db->get()->result_array();
    }

    public function get_program($id)
    {
        $this->db->where('id', $id);
        return $this->db->get('tbl_programs')->row_array();
    }
}