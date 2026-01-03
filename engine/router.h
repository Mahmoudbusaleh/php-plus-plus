#ifndef P_PLUS_PLUS_ROUTER_H
#define P_PLUS_PLUS_ROUTER_H

/**
 * PHP++ Engine Header
 * Definitions for the C++ Bridge.
 */

extern "C" {
    /**
     * Function signature for PHP FFI integration.
     * Declared as extern "C" to prevent C++ name mangling.
     */
    bool match_route(const char* current_url, const char* target_route) noexcept;
}

#endif // P_PLUS_PLUS_ROUTER_H
