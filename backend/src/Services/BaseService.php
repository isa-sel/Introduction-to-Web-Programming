<?php
namespace Ibu\Web\Services;

/**
 * Base Service Class
 * 
 * This abstract class provides common service functionality for all entities.
 * It acts as a wrapper around the DAO layer and provides additional business logic.
 */
abstract class BaseService {
    protected $dao;

    /**
     * Get all records from the associated entity
     * 
     * @return array Array of all records
     */
    public function getAll() {
        return $this->dao->getAll();
    }

    /**
     * Get a single record by ID
     * 
     * @param int $id ID of the record to retrieve
     * @return array|bool The record data or false if not found
     */
    public function getById($id) {
        if (!is_numeric($id) || $id <= 0) {
            throw new \InvalidArgumentException("Invalid ID provided");
        }
        
        $result = $this->dao->getById($id);
        
        if (!$result) {
            throw new \Exception("Record with ID $id not found");
        }
        
        return $result;
    }

    /**
     * Create a new record
     * 
     * @param array $data Data for the new record
     * @return int ID of the newly created record
     */
    public function create($data) {
        $this->validateData($data);
        return $this->dao->insert($data);
    }

    /**
     * Update an existing record
     * 
     * @param int $id ID of the record to update
     * @param array $data New data for the record
     * @return bool Result of the update operation
     */
    public function update($id, $data) {
        if (!is_numeric($id) || $id <= 0) {
            throw new \InvalidArgumentException("Invalid ID provided");
        }
        
        // Check if record exists
        $this->getById($id);
        
        $this->validateData($data, false);
        return $this->dao->update($id, $data);
    }

    /**
     * Delete a record
     * 
     * @param int $id ID of the record to delete
     * @return bool Result of the delete operation
     */
    public function delete($id) {
        if (!is_numeric($id) || $id <= 0) {
            throw new \InvalidArgumentException("Invalid ID provided");
        }
        
        // Check if record exists
        $this->getById($id);
        
        return $this->dao->delete($id);
    }

    /**
     * Validate data before creating or updating
     * 
     * @param array $data Data to validate
     * @param bool $isCreating Whether this is for creating a new record
     * @throws \InvalidArgumentException If validation fails
     */
    abstract protected function validateData($data, $isCreating = true);
}