package com.example.myapplication.model

data class RegisterRequest(
    val name: String,
    val email: String,
    val password: String,
    val passwordConfirmation: String,
)

sealed interface AuthResult {
    data class Success(
        val token: String,
        val permissions: List<String>,
        val warningMessage: String?,
    ) : AuthResult

    data class Error(val message: String) : AuthResult
}