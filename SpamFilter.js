/**
 * SpamFilter Component
 *
 * This component provides a user interface for checking whether an email address
 * is considered spam. It sends the email address to a backend API, displays
 * the result, and handles input validation and error reporting. It also includes
 * a loading indicator to improve user experience during the API call.
 *
 * Usage:
 *
 * <SpamFilter />
 *
 * The component is useful for applications that need to provide users with the
 * ability to verify email addresses, particularly in the context of anti-spam
 * and security.
 */

import React, { useState, useEffect } from 'react';
import axios from 'axios';

// Helper function to validate email format
const validateEmail = (email) => {
    const re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@(([^<>()[\].,;:\s@"]+\.)+[^<>()[\].,;:\s@"]{2,})$/i;
    return re.test(String(email).toLowerCase());
};

const SpamFilter = () => {
    // State variables
    const [email, setEmail] = useState(''); // Email address entered by the user
    const [isSpam, setIsSpam] = useState(null); // Result of spam check (true/false/null)
    const [loading, setLoading] = useState(false); // Indicates whether the API request is in progress
    const [error, setError] = useState(''); // Stores error messages

    // Function to check if the entered email is spam
    const checkEmail = async () => {
        if (!validateEmail(email)) {
            setError('Invalid email format');
            return;
        }
        setError('');
        setLoading(true); // Set loading to true to show the loading indicator
        try {
            // Make a POST request to the backend API to check the email
            const response = await axios.post('/api/check-spam', { email });
            // Update the state with the result of the spam check
            setIsSpam(response.data.isSpam);
        } catch (error) {
            // Update the error state with the error message
            setError('Error checking email');
            console.error('Error checking email:', error);
        } finally {
            // Set loading too false to hide the loading indicator
            setLoading(false);
        }
    };

    // Effect hook to call checkEmail with debounce
    useEffect(() => {
        const timerId = setTimeout(() => {
            if (email) {
                checkEmail().then(() =>{});
            }
        }, 500); // Debounce delay of 500ms
        return () => clearTimeout(timerId);
    }, [email]);

    return (
        <div>
            <input
                type="text"
                value={email}
                onChange={(e) => setEmail(e.target.value)}
                placeholder="Enter email to check"
            />
            {/* Display error message if present */}
            {error && <p className="error">{error}</p>}
            {/* Conditionally render the result or loading indicator */}
            {loading ? (
                <p>Checking...</p>
            ) : (
                isSpam !== null && (
                    <p>{isSpam ? 'This email is likely spam.' : 'This email is not spam.'}</p>
                )
            )}
        </div>
    );
};

export default SpamFilter;
