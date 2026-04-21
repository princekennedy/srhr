package com.example.myapplication.data

import android.content.Context
import androidx.datastore.preferences.core.edit
import androidx.datastore.preferences.core.emptyPreferences
import androidx.datastore.preferences.core.stringPreferencesKey
import androidx.datastore.preferences.core.stringSetPreferencesKey
import androidx.datastore.preferences.preferencesDataStore
import com.example.myapplication.model.AppSettings
import kotlinx.coroutines.flow.Flow
import kotlinx.coroutines.flow.catch
import kotlinx.coroutines.flow.map
import java.io.IOException

private val Context.appDataStore by preferencesDataStore(name = "srhr_connect_settings")

class AppPreferencesRepository(private val context: Context) {
    val settings: Flow<AppSettings> = context.appDataStore.data
        .catch { exception ->
            if (exception is IOException) {
                emit(emptyPreferences())
            } else {
                throw exception
            }
        }
        .map { preferences ->
            AppSettings(
                baseUrl = preferences[Keys.BASE_URL].orEmpty(),
                authToken = preferences[Keys.AUTH_TOKEN],
                personName = preferences[Keys.PERSON_NAME],
                personEmail = preferences[Keys.PERSON_EMAIL],
                permissions = preferences[Keys.PERMISSIONS]?.toList().orEmpty(),
            )
        }

    suspend fun saveBaseUrl(baseUrl: String) {
        context.appDataStore.edit { preferences ->
            preferences[Keys.BASE_URL] = baseUrl.trim().trimEnd('/')
        }
    }

    suspend fun saveSession(token: String, name: String, email: String, permissions: List<String>) {
        context.appDataStore.edit { preferences ->
            preferences[Keys.AUTH_TOKEN] = token
            preferences[Keys.PERSON_NAME] = name
            preferences[Keys.PERSON_EMAIL] = email
            preferences[Keys.PERMISSIONS] = permissions.toSet()
        }
    }

    suspend fun updatePermissions(permissions: List<String>) {
        context.appDataStore.edit { preferences ->
            preferences[Keys.PERMISSIONS] = permissions.toSet()
        }
    }

    suspend fun clearSession() {
        context.appDataStore.edit { preferences ->
            preferences.remove(Keys.AUTH_TOKEN)
            preferences.remove(Keys.PERSON_NAME)
            preferences.remove(Keys.PERSON_EMAIL)
            preferences.remove(Keys.PERMISSIONS)
        }
    }

    private object Keys {
        val BASE_URL = stringPreferencesKey("base_url")
        val AUTH_TOKEN = stringPreferencesKey("auth_token")
        val PERSON_NAME = stringPreferencesKey("person_name")
        val PERSON_EMAIL = stringPreferencesKey("person_email")
        val PERMISSIONS = stringSetPreferencesKey("permissions")
    }
}