<?php
// Set error reporting for debugging
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Bootstrap Laravel application
require __DIR__ . '/../../vendor/autoload.php';
$app = require_once __DIR__ . '/../../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

use App\Models\Contact;
use App\Models\Address;
use App\Models\User;
use Illuminate\Support\Facades\DB;

/**
 * Process API requests in a SOAP-like manner
 */
function processRequest() {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        return [
            'error' => true,
            'message' => 'Only POST requests are supported'
        ];
    }

    $action = $_POST['action'] ?? '';
    $params = $_POST;
    unset($params['action']); // Remove action from params

    switch ($action) {
        // Contact operations
        case 'createContact':
            return createContact($params);
        case 'getContact':
            return getContact($params['contact_id'] ?? 0);
        case 'updateContact':
            return updateContact($params);
        case 'deleteContact':
            return deleteContact($params['contact_id'] ?? 0);
        case 'getAllContacts':
            return getAllContacts($params['user_id'] ?? 0);
            
        // Address operations
        case 'createAddress':
            return createAddress($params);
        case 'getAddress':
            return getAddress($params['address_id'] ?? 0);
        case 'updateAddress':
            return updateAddress($params);
        case 'deleteAddress':
            return deleteAddress($params['address_id'] ?? 0);
        case 'getContactAddresses':
            return getContactAddresses($params['contact_id'] ?? 0);
            
        default:
            return [
                'error' => true,
                'message' => 'Unknown action: ' . $action
            ];
    }
}

/**
 * Create a new contact
 */
function createContact($params) {
    try {
        // Validate user exists
        $user = User::find($params['user_id'] ?? 0);
        if (!$user) {
            return ['success' => false, 'message' => 'User not found'];
        }

        // Create new contact
        $contact = new Contact([
            'user_id' => $params['user_id'],
            'first_name' => $params['first_name'] ?? '',
            'last_name' => $params['last_name'] ?? '',
            'email' => $params['email'] ?? '',
            'phone' => $params['phone'] ?? ''
        ]);
        $contact->save();

        return ['success' => true, 'message' => 'Contact created successfully', 'id' => $contact->id];
    } catch (Exception $e) {
        return ['success' => false, 'message' => 'Error creating contact: ' . $e->getMessage()];
    }
}

/**
 * Get contact by ID
 */
function getContact($id) {
    try {
        $contact = Contact::find($id);
        if (!$contact) {
            return ['success' => false, 'message' => 'Contact not found'];
        }

        return $contact->toArray();
    } catch (Exception $e) {
        return ['success' => false, 'message' => 'Error retrieving contact: ' . $e->getMessage()];
    }
}

/**
 * Update an existing contact
 */
function updateContact($params) {
    try {
        $contact = Contact::find($params['id'] ?? 0);
        if (!$contact) {
            return ['success' => false, 'message' => 'Contact not found'];
        }

        $contact->first_name = $params['first_name'] ?? $contact->first_name;
        $contact->last_name = $params['last_name'] ?? $contact->last_name;
        $contact->email = $params['email'] ?? $contact->email;
        $contact->phone = $params['phone'] ?? $contact->phone;
        $contact->save();

        return ['success' => true, 'message' => 'Contact updated successfully'];
    } catch (Exception $e) {
        return ['success' => false, 'message' => 'Error updating contact: ' . $e->getMessage()];
    }
}

/**
 * Delete a contact
 */
function deleteContact($id) {
    try {
        $contact = Contact::find($id);
        if (!$contact) {
            return ['success' => false, 'message' => 'Contact not found'];
        }

        // Delete related addresses first
        Address::where('contact_id', $id)->delete();
        
        // Delete the contact
        $contact->delete();

        return ['success' => true, 'message' => 'Contact deleted successfully'];
    } catch (Exception $e) {
        return ['success' => false, 'message' => 'Error deleting contact: ' . $e->getMessage()];
    }
}

/**
 * Get all contacts for a user
 */
function getAllContacts($user_id) {
    try {
        $contacts = Contact::where('user_id', $user_id)->get()->toArray();
        return ['contacts' => $contacts];
    } catch (Exception $e) {
        return ['contacts' => [], 'error' => $e->getMessage()];
    }
}

/**
 * Create a new address
 */
function createAddress($params) {
    try {
        // Validate contact exists
        $contact = Contact::find($params['contact_id'] ?? 0);
        if (!$contact) {
            return ['success' => false, 'message' => 'Contact not found'];
        }

        // Create new address
        $address = new Address([
            'contact_id' => $params['contact_id'],
            'street' => $params['street'] ?? '',
            'city' => $params['city'] ?? '',
            'province' => $params['province'] ?? '',
            'country' => $params['country'] ?? '',
            'postal_code' => $params['postal_code'] ?? ''
        ]);
        $address->save();

        return ['success' => true, 'message' => 'Address created successfully', 'id' => $address->id];
    } catch (Exception $e) {
        return ['success' => false, 'message' => 'Error creating address: ' . $e->getMessage()];
    }
}

/**
 * Get address by ID
 */
function getAddress($id) {
    try {
        $address = Address::find($id);
        if (!$address) {
            return ['success' => false, 'message' => 'Address not found'];
        }

        return $address->toArray();
    } catch (Exception $e) {
        return ['success' => false, 'message' => 'Error retrieving address: ' . $e->getMessage()];
    }
}

/**
 * Update an existing address
 */
function updateAddress($params) {
    try {
        $address = Address::find($params['id'] ?? 0);
        if (!$address) {
            return ['success' => false, 'message' => 'Address not found'];
        }

        $address->street = $params['street'] ?? $address->street;
        $address->city = $params['city'] ?? $address->city;
        $address->province = $params['province'] ?? $address->province;
        $address->country = $params['country'] ?? $address->country;
        $address->postal_code = $params['postal_code'] ?? $address->postal_code;
        $address->save();

        return ['success' => true, 'message' => 'Address updated successfully'];
    } catch (Exception $e) {
        return ['success' => false, 'message' => 'Error updating address: ' . $e->getMessage()];
    }
}

/**
 * Delete an address
 */
function deleteAddress($id) {
    try {
        $address = Address::find($id);
        if (!$address) {
            return ['success' => false, 'message' => 'Address not found'];
        }

        $address->delete();

        return ['success' => true, 'message' => 'Address deleted successfully'];
    } catch (Exception $e) {
        return ['success' => false, 'message' => 'Error deleting address: ' . $e->getMessage()];
    }
}

/**
 * Get all addresses for a contact
 */
function getContactAddresses($contact_id) {
    try {
        $addresses = Address::where('contact_id', $contact_id)->get()->toArray();
        return ['addresses' => $addresses];
    } catch (Exception $e) {
        return ['addresses' => [], 'error' => $e->getMessage()];
    }
}

// Process the request and return JSON response
header('Content-Type: application/json');
echo json_encode(processRequest());
