<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class EmpApiService
{
    /**
     * Base URL for the HR Employee API
     */
    private $baseUrl = 'https://appserver.airport.lk/hr_emp_api/api/emp.php';

    /**
     * API Key for authentication
     */
    private $apiKey = 'fa3b2c9c-a96d-48a8-82ad-0cb775dd3e5d';

    /**
     * Make API request to the HR Employee API
     */
    public function makeRequest($type, $id = '')
    {
        try {
            $response = Http::withOptions([
                'verify' => false // Disable SSL certificate verification
            ])->withHeaders([
                'x-api-key' => $this->apiKey // set the api key 
            ])->get($this->baseUrl, [
                'type' => $type, // pass request type to the api
                'id' => $id, //pass request id to the api 
            ]);

            if ($response->successful()) {
                return $response->json();
            }
            throw new \Exception('API request failed', $response->status());
        } catch (\Exception $e) {
            Log::error('API Request Error: ' . $e->getMessage());
            return null;
        }
    }

    public function mapEmployeeToParticipantFields($employeeDetails)
    {
        // Ensure we're working with the first employee in the response
        $employee = is_array($employeeDetails) && isset($employeeDetails[0])
            ? $employeeDetails[0]
            : $employeeDetails;

        // Map the fields
        return [
            'epf_number' => $employee['service_no'] ?? null,
            'name' => $employee['name'] ?? null,
            'date_of_birth' => $employee['DOB'] ?? null,
            'salary_scale_id' => $employee['salary_scale_id'] ?? null
        ];
    }

    public function getEmployeeDetailsForParticipant($epfNumber)
    {
        // First, get employee details
        $employeeResponse = $this->getEmployeeByEPF($epfNumber);

        if (!$employeeResponse) {
            return null;
        }

        // Map employee details to participant form fields
        return $this->mapEmployeeToParticipantFields($employeeResponse);
    }

    /**
     * Calculate age based on date of birth
     */
    private function calculateAge($dateOfBirth)
    {
        if (!$dateOfBirth) {
            return null;
        }

        try {
            $birthDate = Carbon::parse($dateOfBirth);
            return Carbon::now()->diffInYears($birthDate);
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Get employee by EPF number
     */
    public function getEmployeeByEPF($epfNumber)
    {
        return $this->makeRequest('emp', $epfNumber);
    }
}
