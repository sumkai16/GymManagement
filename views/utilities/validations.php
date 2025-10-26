<?php
/**
 * Validation Utilities
 * Contains server-side validation functions for forms
 */

class Validation {
    /**
     * Validate email address
     */
    public static function validateEmail($email) {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }

    /**
     * Validate phone number (basic validation)
     */
    public static function validatePhone($phone) {
        if (empty($phone)) return true; // Optional field
        // Remove spaces, dashes, parentheses
        $cleaned = preg_replace('/[\s\-\(\)]/', '', $phone);
        // Check if it starts with + and has 1-16 digits, or just 1-16 digits
        return preg_match('/^[\+]?[1-9][\d]{0,15}$/', $cleaned);
    }

    /**
     * Validate date string
     */
    public static function validateDate($dateString) {
        $date = DateTime::createFromFormat('Y-m-d', $dateString);
        return $date && $date->format('Y-m-d') === $dateString;
    }

    /**
     * Validate required field
     */
    public static function validateRequired($value) {
        return !empty(trim($value));
    }

    /**
     * Validate minimum length
     */
    public static function validateMinLength($value, $minLength) {
        return strlen(trim($value)) >= $minLength;
    }

    /**
     * Validate maximum length
     */
    public static function validateMaxLength($value, $maxLength) {
        return strlen(trim($value)) <= $maxLength;
    }

    /**
     * Validate username (alphanumeric, underscore, dash, min 3 chars)
     */
    public static function validateUsername($username) {
        return preg_match('/^[a-zA-Z0-9_-]{3,}$/', $username);
    }

    /**
     * Validate password strength (min 6 chars)
     */
    public static function validatePassword($password) {
        return strlen($password) >= 6;
    }

    /**
     * Validate member data for add/edit operations
     */
    public static function validateMemberData($data, $isEdit = false) {
        $errors = [];

        // Full Name validation
        if (!self::validateRequired($data['full_name'] ?? '')) {
            $errors['full_name'] = 'Full Name is required.';
        } elseif (!self::validateMaxLength($data['full_name'], 100)) {
            $errors['full_name'] = 'Full Name must be less than 100 characters.';
        }

        // Email validation
        if (!self::validateRequired($data['email'] ?? '')) {
            $errors['email'] = 'Email is required.';
        } elseif (!self::validateEmail($data['email'])) {
            $errors['email'] = 'Please enter a valid email address.';
        }

        // Phone validation (optional)
        if (!empty($data['phone']) && !self::validatePhone($data['phone'])) {
            $errors['phone'] = 'Please enter a valid phone number.';
        }

        // Address validation (optional)
        if (!empty($data['address']) && !self::validateMaxLength($data['address'], 255)) {
            $errors['address'] = 'Address must be less than 255 characters.';
        }

        // Membership Type validation
        $validMembershipTypes = ['monthly', 'annual'];
        if (!self::validateRequired($data['membership_type'] ?? '')) {
            $errors['membership_type'] = 'Membership Type is required.';
        } elseif (!in_array($data['membership_type'], $validMembershipTypes)) {
            $errors['membership_type'] = 'Invalid membership type.';
        }

        // Start Date validation
        if (!self::validateRequired($data['start_date'] ?? '')) {
            $errors['start_date'] = 'Start Date is required.';
        } elseif (!self::validateDate($data['start_date'])) {
            $errors['start_date'] = 'Please enter a valid start date.';
        } elseif (!$isEdit) {
            // For new members, start date cannot be in the past
            $today = new DateTime();
            $today->setTime(0, 0, 0);
            $startDate = new DateTime($data['start_date']);
            if ($startDate < $today) {
                $errors['start_date'] = 'Start Date cannot be in the past.';
            }
        }

        // End Date validation
        if (!self::validateRequired($data['end_date'] ?? '')) {
            $errors['end_date'] = 'End Date is required.';
        } elseif (!self::validateDate($data['end_date'])) {
            $errors['end_date'] = 'Please enter a valid end date.';
        } elseif (!empty($data['start_date']) && self::validateDate($data['start_date'])) {
            $startDate = new DateTime($data['start_date']);
            $endDate = new DateTime($data['end_date']);
            if ($endDate <= $startDate) {
                $errors['end_date'] = 'End Date must be after Start Date.';
            }
        }

        // Status validation
        $validStatuses = ['active', 'inactive'];
        if (!self::validateRequired($data['status'] ?? '')) {
            $errors['status'] = 'Status is required.';
        } elseif (!in_array($data['status'], $validStatuses)) {
            $errors['status'] = 'Invalid status.';
        }

        // User account validation (for new users)
        if (isset($data['user_option']) && $data['user_option'] === 'new') {
            if (!self::validateRequired($data['username'] ?? '')) {
                $errors['username'] = 'Username is required.';
            } elseif (!self::validateUsername($data['username'])) {
                $errors['username'] = 'Username must be at least 3 characters and contain only letters, numbers, underscores, and dashes.';
            }

            if (!self::validateRequired($data['password'] ?? '')) {
                $errors['password'] = 'Password is required.';
            } elseif (!self::validatePassword($data['password'])) {
                $errors['password'] = 'Password must be at least 6 characters long.';
            }
        }

        return $errors;
    }

    /**
     * Sanitize input data
     */
    public static function sanitizeInput($data) {
        if (is_array($data)) {
            return array_map([self::class, 'sanitizeInput'], $data);
        }
        return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
    }

    /**
     * Validate and sanitize member form data
     */
    public static function processMemberFormData($postData) {
        $sanitized = self::sanitizeInput($postData);
        $errors = self::validateMemberData($sanitized);

        return [
            'data' => $sanitized,
            'errors' => $errors,
            'isValid' => empty($errors)
        ];
    }
}
?>
