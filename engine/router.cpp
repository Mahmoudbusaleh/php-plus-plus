// engine/router.cpp
#include <string.h>

extern "C" {
    // A high-speed C function to check if two routes match
    bool match_route(const char* current_url, const char* target_route) {
        if (strcmp(current_url, target_route) == 0) {
            return true;
        }
        return false;
    }
}
