#include <cstring>

/**
 * PHP++ Engine Core
 * Implementation of high-performance string matching.
 */

extern "C" {
    /**
     * Matches the current request URL against a target route.
     * * @param current_url The URL from the browser (e.g., /home)
     * @param target_route The registered route pattern
     * @return bool True (1) if they match exactly, False (0) otherwise
     */
    bool match_route(const char* current_url, const char* target_route) noexcept {
        // Safety check: Ensure pointers are not null to prevent segmentation faults
        if (!current_url || !target_route) {
            return false;
        }

        // Using standard C strcmp for maximum execution speed
        // This is much faster than std::string comparison for raw routing
        return (std::strcmp(current_url, target_route) == 0);
    }
}
