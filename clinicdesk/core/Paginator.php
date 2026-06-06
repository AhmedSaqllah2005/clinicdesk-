<?php
class Paginator {
    private $totalItems;
    private $perPage;
    private $currentPage;
    
    public function __construct($totalItems, $perPage, $currentPage = 1) {
        $this->totalItems = $totalItems;
        $this->perPage = $perPage;
        $this->currentPage = max(1, $currentPage);
    }
    
    public function offset() {
        return ($this->currentPage - 1) * $this->perPage;
    }
    
    public function totalPages() {
        return ceil($this->totalItems / $this->perPage);
    }
    
    public function hasPrev() {
        return $this->currentPage > 1;
    }
    
    public function hasNext() {
        return $this->currentPage < $this->totalPages();
    }
    
    public function currentPage() {
        return $this->currentPage;
    }
    
    public function getPrevPage() {
        return $this->currentPage - 1;
    }
    
    public function getNextPage() {
        return $this->currentPage + 1;
    }
}