<?php

class Course extends CI_Model
{
    /* Creates a new course */
    public function insert_course($data)
    {
        $courseName      = $data['course_name'];
        $courseCode      = $data['course_code'];
        $courseDept      = $data['course_dept'];
        $courseDesc      = $data['course_desc'];
        $courseSeats     = $data['course_seats_available'];
        $faculty_user_id = $data['faculty_user_id'];

        $data = array(
            'name' => $courseName,
            'code' => $courseCode,
            'description' => $courseDesc,
            'seats_available' => $courseSeats,
            'department_id' => $courseDept,
            'faculty_user_id' => $faculty_user_id
        );

        $this->db->set($data);
        $this->db->insert('courses');
        return $this->db->insert_id(); // Inserted course ID
    }

    public function getIdByCourseCode($code)
    {
        $this->db->select("id");
        $query = $this->db->get_where('courses', array('code' => $code));

        if ($query->num_rows() > 0) {
            return $query->first_row()->id;
        }
        return false;
    }

    public function getCoursesByFacultyId($userId)
    {
        $this->db->select("*");
        $query = $this->db->get_where('courses', array('faculty_user_id' => $userId));

        if ($query->num_rows() > 0) {
            return $query->result_array();
        }
        return false;
    }

    public function getAvailableTimeSlots()
    {
        $query = $this->db->get('timeslots_available');

        if ($query->num_rows() > 0) {
            return $query->result_array();
        }
        return false;
    }

    public function getAllCourses()
    {
        $query = $this->db->get('courses');

        if ($query->num_rows() > 0) {
            return $query->result_array();
        }
        return false;
    }

    public function getCourseById($id)
    {
        $this->db->select("*");
        $query = $this->db->get_where('courses', array('id' => $id));

        if ($query->num_rows() > 0) {
            return $query->row_array();
        }
        return false;
    }

    public function getAvailableSeats($courseId)
    {
        $this->db->select("seats_available");
        $query = $this->db->get_where('courses', array('id' => $courseId));

        if ($query->num_rows() > 0) {
            return $query->first_row()->seats_available;
        }
        return false;
    }

    public function studentEnroll($data)
    {
        $courseSection = $data['course_student_enroll_section'];
        $courseId      = $data['course_student_enroll_course_id'];
        $seats         = $data['course_student_enroll_course_seats_avbl'];
        $studentId     = $data['student_id'];

        $data = array(
            'student_id' => $studentId,
            'course_id' => $courseId,
            'section_id' => $courseSection,
            'date' => date('Y-m-d'),
        );

        $this->db->set($data);
        $this->db->insert('course_enrolled');

        $course_enrollment_insert = $this->db->insert_id(); // Inserted course ID

        /* Update the number of seats in course table */
        $seats_available_current = $this->getAvailableSeats($courseId);
        $this->db->set('seats_available', intval($seats_available_current) - 1);
        $this->db->where('id', $courseId);
        $this->db->update('courses');

        return $course_enrollment_insert;
    }

    public function getAllCoursesByStudentId($studentId)
    {
        $this->db->select("*");
        $query = $this->db->get_where('course_enrolled', array('student_id' => $studentId));

        if ($query->num_rows() > 0) {
            return $query->result_array();
        }
        return false;
    }

    public function getAllCoursesByFacultyId($facultyId)
    {
        $this->db->select("*");
        $query = $this->db->get_where('courses', array('faculty_user_id' => $facultyId));

        if ($query->num_rows() > 0) {
            return $query->result_array();
        }
        return false;
    }

    public function getStudentsFromCourse($courseId)
    {
        $this->db->select("*");
        $query = $this->db->get_where('course_enrolled', array('course_id' => $courseId));

        if ($query->num_rows() > 0) {
            return $query->result_array();
        }
        return false;
    }

    public function updateCourseFileName($name, $courseId)
    {
        $this->db->set('file_name', $name);
        $this->db->where('id', $courseId);
        $this->db->update('courses');

        if ($this->db->affected_rows()) {
            return true;
        }
        return false;
    }

    public function getCourseFileName($courseId)
    {
        $this->db->select("file_name");
        $query = $this->db->get_where('courses', array('id' => $courseId));

        if ($query->num_rows() > 0) {
            return $query->first_row()->file_name;
        }
        return false;
    }


}

?>