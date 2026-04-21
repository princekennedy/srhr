package com.example.myapplication.model

data class AppSettings(
    val baseUrl: String = "",
    val authToken: String? = null,
    val personName: String? = null,
    val personEmail: String? = null,
    val permissions: List<String> = emptyList(),
)