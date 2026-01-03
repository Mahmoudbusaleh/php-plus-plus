#include <iostream>
#include <string>
#include <cstring>

// Essential for FFI: This prevents name mangling so PHP can find the function
extern "C" {

    /**
     * High-performance string matching for routing
     * Returns 1 (true) if matched, 0 (false) otherwise
     */
    int match_route(const char* current_url, const char* target_route) {
        // Null checks to prevent crashes
        if (!current_url || !target_route) {
            return 0;
        }

        // Basic string comparison
        // In the future, we can implement Trie Tree or Hash Map here for O(1) performance
        if (std::strcmp(current_url, target_route) == 0) {
            return 1;
        }

        return 0;
    }
}
