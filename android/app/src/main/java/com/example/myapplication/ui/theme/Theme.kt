package com.example.myapplication.ui.theme

import androidx.compose.foundation.isSystemInDarkTheme
import androidx.compose.material3.MaterialTheme
import androidx.compose.material3.darkColorScheme
import androidx.compose.material3.lightColorScheme
import androidx.compose.runtime.Composable

private val LightColors = lightColorScheme(
    primary = Lagoon,
    onPrimary = Mist,
    secondary = Coral,
    onSecondary = Charcoal,
    tertiary = LagoonDeep,
    background = Mist,
    onBackground = Charcoal,
    surface = Sand,
    onSurface = Charcoal,
    surfaceVariant = CoralSoft,
    onSurfaceVariant = Stone,
)

private val DarkColors = darkColorScheme(
    primary = SeaGlass,
    onPrimary = Charcoal,
    secondary = Coral,
    onSecondary = Charcoal,
    tertiary = Lagoon,
    background = Charcoal,
    onBackground = Mist,
    surface = Slate,
    onSurface = Mist,
    surfaceVariant = LagoonDeep,
    onSurfaceVariant = Sand,
)

@Composable
fun SrhrTheme(
    darkTheme: Boolean = isSystemInDarkTheme(),
    content: @Composable () -> Unit,
) {
    MaterialTheme(
        colorScheme = if (darkTheme) DarkColors else LightColors,
        content = content,
    )
}