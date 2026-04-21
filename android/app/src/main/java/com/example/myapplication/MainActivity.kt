package com.example.myapplication

import android.os.Bundle
import androidx.activity.ComponentActivity
import androidx.activity.compose.setContent
import androidx.compose.foundation.background
import androidx.compose.foundation.layout.Arrangement
import androidx.compose.foundation.layout.Box
import androidx.compose.foundation.layout.Column
import androidx.compose.foundation.layout.PaddingValues
import androidx.compose.foundation.layout.Row
import androidx.compose.foundation.layout.fillMaxSize
import androidx.compose.foundation.layout.fillMaxWidth
import androidx.compose.foundation.layout.padding
import androidx.compose.foundation.layout.size
import androidx.compose.foundation.lazy.LazyColumn
import androidx.compose.foundation.lazy.items
import androidx.compose.foundation.shape.CircleShape
import androidx.compose.foundation.shape.RoundedCornerShape
import androidx.compose.material.icons.Icons
import androidx.compose.material.icons.automirrored.filled.ArrowBack
import androidx.compose.material.icons.filled.Lock
import androidx.compose.material.icons.filled.Public
import androidx.compose.material.icons.filled.Settings
import androidx.compose.material.icons.filled.Sync
import androidx.compose.material3.AssistChip
import androidx.compose.material3.Button
import androidx.compose.material3.Card
import androidx.compose.material3.CircularProgressIndicator
import androidx.compose.material3.ElevatedCard
import androidx.compose.material3.ExperimentalMaterial3Api
import androidx.compose.material3.Icon
import androidx.compose.material3.IconButton
import androidx.compose.material3.MaterialTheme
import androidx.compose.material3.OutlinedButton
import androidx.compose.material3.OutlinedTextField
import androidx.compose.material3.Scaffold
import androidx.compose.material3.SnackbarHost
import androidx.compose.material3.SnackbarHostState
import androidx.compose.material3.Surface
import androidx.compose.material3.Text
import androidx.compose.material3.TextButton
import androidx.compose.material3.TopAppBar
import androidx.compose.runtime.LaunchedEffect
import androidx.compose.runtime.Composable
import androidx.compose.runtime.collectAsState
import androidx.compose.runtime.getValue
import androidx.compose.runtime.mutableStateOf
import androidx.compose.runtime.remember
import androidx.compose.runtime.rememberCoroutineScope
import androidx.compose.runtime.saveable.rememberSaveable
import androidx.compose.runtime.setValue
import androidx.compose.ui.Alignment
import androidx.compose.ui.Modifier
import androidx.compose.ui.graphics.Brush
import androidx.compose.ui.graphics.Color
import androidx.compose.ui.graphics.vector.ImageVector
import androidx.compose.ui.text.font.FontWeight
import androidx.compose.ui.text.input.PasswordVisualTransformation
import androidx.compose.ui.text.style.TextAlign
import androidx.compose.ui.unit.dp
import androidx.navigation.NavHostController
import androidx.navigation.compose.NavHost
import androidx.navigation.compose.composable
import androidx.navigation.compose.rememberNavController
import com.example.myapplication.data.AppPreferencesRepository
import com.example.myapplication.data.AuthRepository
import com.example.myapplication.data.CmsRepository
import com.example.myapplication.model.AppSettings
import com.example.myapplication.model.AppBootstrap
import com.example.myapplication.model.AuthResult
import com.example.myapplication.model.CmsContent
import com.example.myapplication.model.CmsFaq
import com.example.myapplication.model.CmsMenuItem
import com.example.myapplication.model.CmsQuiz
import com.example.myapplication.model.CmsServiceCenter
import com.example.myapplication.model.RegisterRequest
import com.example.myapplication.ui.theme.Coral
import com.example.myapplication.ui.theme.Lagoon
import com.example.myapplication.ui.theme.SrhrTheme
import kotlinx.coroutines.launch

class MainActivity : ComponentActivity() {
    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)

        val preferencesRepository = AppPreferencesRepository(applicationContext)
        val authRepository = AuthRepository()
        val cmsRepository = CmsRepository()

        setContent {
            SrhrTheme {
                Surface(modifier = Modifier.fillMaxSize(), color = MaterialTheme.colorScheme.background) {
                    SrhrApp(preferencesRepository = preferencesRepository, authRepository = authRepository, cmsRepository = cmsRepository)
                }
            }
        }
    }
}

private enum class Destination(val route: String) {
    Welcome("welcome"),
    Register("register"),
    PublicSpace("public-space"),
    PersonSpace("person-space"),
    Settings("settings"),
}

@Composable
private fun SrhrApp(
    preferencesRepository: AppPreferencesRepository,
    authRepository: AuthRepository,
    cmsRepository: CmsRepository,
) {
    val navController = rememberNavController()
    val settings by preferencesRepository.settings.collectAsState(initial = AppSettings())

    NavHost(navController = navController, startDestination = Destination.Welcome.route) {
        composable(Destination.Welcome.route) {
            WelcomeScreen(
                settings = settings,
                onOpenPersonSpace = {
                    navController.navigate(
                        if (settings.authToken.isNullOrBlank()) Destination.Register.route else Destination.PersonSpace.route,
                    )
                },
                onOpenPublicSpace = { navController.navigate(Destination.PublicSpace.route) },
                onOpenSettings = { navController.navigate(Destination.Settings.route) },
            )
        }
        composable(Destination.Register.route) {
            RegistrationScreen(
                settings = settings,
                authRepository = authRepository,
                preferencesRepository = preferencesRepository,
                onBack = { navController.popBackStack() },
                onOpenSettings = { navController.navigate(Destination.Settings.route) },
                onRegistered = {
                    navController.navigate(Destination.PersonSpace.route) {
                        popUpTo(Destination.Welcome.route)
                    }
                },
            )
        }
        composable(Destination.PublicSpace.route) {
            PublicSpaceScreen(
                settings = settings,
                cmsRepository = cmsRepository,
                onBack = { navController.popBackStack() },
                onOpenSettings = { navController.navigate(Destination.Settings.route) },
            )
        }
        composable(Destination.PersonSpace.route) {
            PersonSpaceScreen(
                settings = settings,
                authRepository = authRepository,
                preferencesRepository = preferencesRepository,
                onBack = { navController.popBackStack() },
                onRegister = { navController.navigate(Destination.Register.route) },
            )
        }
        composable(Destination.Settings.route) {
            SettingsScreen(
                settings = settings,
                preferencesRepository = preferencesRepository,
                navController = navController,
            )
        }
    }
}

@OptIn(ExperimentalMaterial3Api::class)
@Composable
private fun WelcomeScreen(
    settings: AppSettings,
    onOpenPersonSpace: () -> Unit,
    onOpenPublicSpace: () -> Unit,
    onOpenSettings: () -> Unit,
) {
    Scaffold(
        topBar = {
            TopAppBar(
                title = { Text("SRHR Connect") },
                actions = {
                    IconButton(onClick = onOpenSettings) {
                        Icon(Icons.Filled.Settings, contentDescription = "Settings")
                    }
                },
            )
        },
    ) { paddingValues ->
        LazyColumn(
            modifier = Modifier
                .fillMaxSize()
                .padding(paddingValues),
            contentPadding = PaddingValues(20.dp),
            verticalArrangement = Arrangement.spacedBy(18.dp),
        ) {
            item {
                Box(
                    modifier = Modifier
                        .fillMaxWidth()
                        .background(
                            brush = Brush.linearGradient(listOf(Lagoon, Coral)),
                            shape = RoundedCornerShape(32.dp),
                        )
                        .padding(24.dp),
                ) {
                    Column(verticalArrangement = Arrangement.spacedBy(10.dp)) {
                        Text(
                            text = "Welcome",
                            style = MaterialTheme.typography.labelLarge,
                            color = Color.White.copy(alpha = 0.8f),
                        )
                        Text(
                            text = "Choose a private person space or a public learning space.",
                            style = MaterialTheme.typography.headlineMedium,
                            color = Color.White,
                            fontWeight = FontWeight.Bold,
                        )
                        Text(
                            text = "Person Space requires registration and will pull permission data from the backend. Public Space keeps general information and resources open to everyone.",
                            style = MaterialTheme.typography.bodyLarge,
                            color = Color.White.copy(alpha = 0.92f),
                        )
                    }
                }
            }
            item {
                StatusStrip(baseUrl = settings.baseUrl, hasSession = !settings.authToken.isNullOrBlank())
            }
            item {
                SpaceChoiceCard(
                    title = "Person Space",
                    description = "Registered-only space for personalized access, permissions, and future saved content.",
                    icon = Icons.Filled.Lock,
                    accent = MaterialTheme.colorScheme.primary,
                    actionLabel = if (settings.authToken.isNullOrBlank()) "Register to continue" else "Open person space",
                    onClick = onOpenPersonSpace,
                )
            }
            item {
                SpaceChoiceCard(
                    title = "Public Space",
                    description = "General information, safe guidance, and resource-focused content without sign-in.",
                    icon = Icons.Filled.Public,
                    accent = MaterialTheme.colorScheme.secondary,
                    actionLabel = "Enter public space",
                    onClick = onOpenPublicSpace,
                )
            }
        }
    }
}

@Composable
private fun StatusStrip(baseUrl: String, hasSession: Boolean) {
    ElevatedCard(shape = RoundedCornerShape(24.dp)) {
        Column(modifier = Modifier.padding(20.dp), verticalArrangement = Arrangement.spacedBy(8.dp)) {
            Text("Backend status", style = MaterialTheme.typography.titleMedium, fontWeight = FontWeight.SemiBold)
            Text(
                text = if (baseUrl.isBlank()) {
                    "No API base URL configured yet. Open settings and set one, for example http://10.0.2.2:8000 for a local Laravel backend in the emulator."
                } else {
                    "Current base URL: $baseUrl"
                },
                style = MaterialTheme.typography.bodyMedium,
                color = MaterialTheme.colorScheme.onSurfaceVariant,
            )
            Text(
                text = if (hasSession) "A person-space session is stored on this device." else "No person-space session is stored yet.",
                style = MaterialTheme.typography.bodySmall,
                color = MaterialTheme.colorScheme.onSurfaceVariant,
            )
        }
    }
}

@Composable
private fun SpaceChoiceCard(
    title: String,
    description: String,
    icon: ImageVector,
    accent: Color,
    actionLabel: String,
    onClick: () -> Unit,
) {
    Card(shape = RoundedCornerShape(28.dp)) {
        Column(
            modifier = Modifier
                .fillMaxWidth()
                .padding(22.dp),
            verticalArrangement = Arrangement.spacedBy(12.dp),
        ) {
            Box(
                modifier = Modifier
                    .size(52.dp)
                    .background(accent.copy(alpha = 0.15f), CircleShape),
                contentAlignment = Alignment.Center,
            ) {
                Icon(icon, contentDescription = null, tint = accent)
            }
            Text(title, style = MaterialTheme.typography.headlineSmall, fontWeight = FontWeight.Bold)
            Text(description, style = MaterialTheme.typography.bodyLarge, color = MaterialTheme.colorScheme.onSurfaceVariant)
            Button(onClick = onClick, shape = RoundedCornerShape(16.dp)) {
                Text(actionLabel)
            }
        }
    }
}

@OptIn(ExperimentalMaterial3Api::class)
@Composable
private fun RegistrationScreen(
    settings: AppSettings,
    authRepository: AuthRepository,
    preferencesRepository: AppPreferencesRepository,
    onBack: () -> Unit,
    onOpenSettings: () -> Unit,
    onRegistered: () -> Unit,
) {
    var name by rememberSaveable { mutableStateOf("") }
    var email by rememberSaveable { mutableStateOf("") }
    var password by rememberSaveable { mutableStateOf("") }
    var confirmPassword by rememberSaveable { mutableStateOf("") }
    var isLoading by rememberSaveable { mutableStateOf(false) }
    var errorMessage by rememberSaveable { mutableStateOf<String?>(null) }
    var warningMessage by rememberSaveable { mutableStateOf<String?>(null) }
    val scope = rememberCoroutineScope()

    Scaffold(
        topBar = {
            TopAppBar(
                title = { Text("Create your person space") },
                navigationIcon = {
                    IconButton(onClick = onBack) {
                        Icon(Icons.AutoMirrored.Filled.ArrowBack, contentDescription = "Back")
                    }
                },
                actions = {
                    TextButton(onClick = onOpenSettings) {
                        Text("Settings")
                    }
                },
            )
        },
    ) { paddingValues ->
        LazyColumn(
            modifier = Modifier
                .fillMaxSize()
                .padding(paddingValues),
            contentPadding = PaddingValues(20.dp),
            verticalArrangement = Arrangement.spacedBy(16.dp),
        ) {
            item {
                Text(
                    text = "Registration uses the configured backend base URL and then requests permission data for the new account.",
                    style = MaterialTheme.typography.bodyLarge,
                    color = MaterialTheme.colorScheme.onSurfaceVariant,
                )
            }
            item {
                OutlinedTextField(
                    value = name,
                    onValueChange = { name = it },
                    label = { Text("Full name") },
                    modifier = Modifier.fillMaxWidth(),
                    singleLine = true,
                )
            }
            item {
                OutlinedTextField(
                    value = email,
                    onValueChange = { email = it },
                    label = { Text("Email address") },
                    modifier = Modifier.fillMaxWidth(),
                    singleLine = true,
                )
            }
            item {
                OutlinedTextField(
                    value = password,
                    onValueChange = { password = it },
                    label = { Text("Password") },
                    modifier = Modifier.fillMaxWidth(),
                    visualTransformation = PasswordVisualTransformation(),
                    singleLine = true,
                )
            }
            item {
                OutlinedTextField(
                    value = confirmPassword,
                    onValueChange = { confirmPassword = it },
                    label = { Text("Confirm password") },
                    modifier = Modifier.fillMaxWidth(),
                    visualTransformation = PasswordVisualTransformation(),
                    singleLine = true,
                )
            }
            item {
                if (errorMessage != null || warningMessage != null) {
                    ElevatedCard(shape = RoundedCornerShape(20.dp)) {
                        Column(modifier = Modifier.padding(16.dp), verticalArrangement = Arrangement.spacedBy(8.dp)) {
                            errorMessage?.let {
                                Text(it, color = MaterialTheme.colorScheme.error)
                            }
                            warningMessage?.let {
                                Text(it, color = MaterialTheme.colorScheme.secondary)
                            }
                        }
                    }
                }
            }
            item {
                Button(
                    modifier = Modifier.fillMaxWidth(),
                    enabled = !isLoading,
                    onClick = {
                        errorMessage = null
                        warningMessage = null

                        when {
                            settings.baseUrl.isBlank() -> errorMessage = "Set the API base URL in Settings before registering."
                            name.isBlank() || email.isBlank() || password.isBlank() || confirmPassword.isBlank() -> errorMessage = "Fill in all registration fields."
                            password != confirmPassword -> errorMessage = "Passwords do not match."
                            else -> {
                                isLoading = true
                                scope.launch {
                                    when (
                                        val result = authRepository.registerAndLoadPermissions(
                                            baseUrl = settings.baseUrl,
                                            request = RegisterRequest(
                                                name = name.trim(),
                                                email = email.trim(),
                                                password = password,
                                                passwordConfirmation = confirmPassword,
                                            ),
                                        )
                                    ) {
                                        is AuthResult.Error -> errorMessage = result.message
                                        is AuthResult.Success -> {
                                            preferencesRepository.saveSession(
                                                token = result.token,
                                                name = name.trim(),
                                                email = email.trim(),
                                                permissions = result.permissions,
                                            )
                                            warningMessage = result.warningMessage
                                            onRegistered()
                                        }
                                    }

                                    isLoading = false
                                }
                            }
                        }
                    },
                ) {
                    if (isLoading) {
                        CircularProgressIndicator(modifier = Modifier.size(18.dp), strokeWidth = 2.dp)
                    } else {
                        Text("Register and load permissions")
                    }
                }
            }
        }
    }
}

@OptIn(ExperimentalMaterial3Api::class)
@Composable
private fun PublicSpaceScreen(
    settings: AppSettings,
    cmsRepository: CmsRepository,
    onBack: () -> Unit,
    onOpenSettings: () -> Unit,
) {
    var bootstrap by remember { mutableStateOf<AppBootstrap?>(null) }
    var isLoading by rememberSaveable { mutableStateOf(false) }
    var errorMessage by rememberSaveable { mutableStateOf<String?>(null) }
    var refreshKey by rememberSaveable { mutableStateOf(0) }

    LaunchedEffect(settings.baseUrl, settings.authToken, refreshKey) {
        if (settings.baseUrl.isBlank()) {
            bootstrap = null
            errorMessage = "Set the backend base URL in Settings to load public CMS data."
            isLoading = false
            return@LaunchedEffect
        }

        isLoading = true
        errorMessage = null

        val result = cmsRepository.fetchBootstrap(settings.baseUrl, settings.authToken)
        result.onSuccess {
            bootstrap = it
        }.onFailure { error ->
            bootstrap = null
            errorMessage = error.message ?: "Could not load CMS content."
        }

        isLoading = false
    }

    Scaffold(
        topBar = {
            TopAppBar(
                title = { Text("Public Space") },
                navigationIcon = {
                    IconButton(onClick = onBack) {
                        Icon(Icons.AutoMirrored.Filled.ArrowBack, contentDescription = "Back")
                    }
                },
                actions = {
                    IconButton(onClick = { refreshKey += 1 }) {
                        Icon(Icons.Filled.Sync, contentDescription = "Refresh")
                    }
                    IconButton(onClick = onOpenSettings) {
                        Icon(Icons.Filled.Settings, contentDescription = "Settings")
                    }
                },
            )
        },
    ) { paddingValues ->
        LazyColumn(
            modifier = Modifier
                .fillMaxSize()
                .padding(paddingValues),
            contentPadding = PaddingValues(20.dp),
            verticalArrangement = Arrangement.spacedBy(16.dp),
        ) {
            item {
                ElevatedCard(shape = RoundedCornerShape(24.dp)) {
                    Column(modifier = Modifier.padding(20.dp), verticalArrangement = Arrangement.spacedBy(8.dp)) {
                        Text(
                            text = bootstrap?.settingValue("app_name") ?: "SRHR Connect",
                            style = MaterialTheme.typography.headlineSmall,
                            fontWeight = FontWeight.Bold,
                        )
                        Text(
                            text = bootstrap?.settingValue("welcome_message")
                                ?: "Public Space exposes CMS-managed information, FAQs, quizzes, and service links without sign-in.",
                            style = MaterialTheme.typography.bodyLarge,
                            color = MaterialTheme.colorScheme.onSurfaceVariant,
                        )
                    }
                }
            }

            if (isLoading) {
                item {
                    ElevatedCard(shape = RoundedCornerShape(24.dp)) {
                        Row(
                            modifier = Modifier
                                .fillMaxWidth()
                                .padding(20.dp),
                            horizontalArrangement = Arrangement.spacedBy(12.dp),
                            verticalAlignment = Alignment.CenterVertically,
                        ) {
                            CircularProgressIndicator(modifier = Modifier.size(22.dp), strokeWidth = 2.dp)
                            Text("Loading CMS modules from ${settings.baseUrl}...")
                        }
                    }
                }
            }

            errorMessage?.let { message ->
                item {
                    ElevatedCard(shape = RoundedCornerShape(24.dp)) {
                        Column(modifier = Modifier.padding(20.dp), verticalArrangement = Arrangement.spacedBy(8.dp)) {
                            Text("Public content unavailable", style = MaterialTheme.typography.titleMedium, fontWeight = FontWeight.SemiBold)
                            Text(message, color = MaterialTheme.colorScheme.error)
                        }
                    }
                }
            }

            bootstrap?.let { data ->
                item {
                    Text("Menu", style = MaterialTheme.typography.titleLarge, fontWeight = FontWeight.SemiBold)
                }
                items(data.menuItems) { item ->
                    PublicMenuCard(item)
                }

                item {
                    Text("Categories", style = MaterialTheme.typography.titleLarge, fontWeight = FontWeight.SemiBold)
                }
                item {
                    ElevatedCard(shape = RoundedCornerShape(24.dp)) {
                        Column(modifier = Modifier.padding(20.dp), verticalArrangement = Arrangement.spacedBy(10.dp)) {
                            data.categories.forEach { category ->
                                AssistChip(
                                    onClick = {},
                                    label = { Text("${category.name} (${category.contentsCount})") },
                                )
                                category.description?.let {
                                    Text(it, style = MaterialTheme.typography.bodySmall, color = MaterialTheme.colorScheme.onSurfaceVariant)
                                }
                            }
                        }
                    }
                }

                item {
                    Text("Featured content", style = MaterialTheme.typography.titleLarge, fontWeight = FontWeight.SemiBold)
                }
                items(data.featuredContents) { content ->
                    PublicContentCard(content)
                }

                item {
                    Text("FAQs", style = MaterialTheme.typography.titleLarge, fontWeight = FontWeight.SemiBold)
                }
                items(data.faqs) { faq ->
                    PublicFaqCard(faq)
                }

                item {
                    Text("Quizzes", style = MaterialTheme.typography.titleLarge, fontWeight = FontWeight.SemiBold)
                }
                items(data.quizzes) { quiz ->
                    PublicQuizCard(quiz)
                }

                item {
                    Text("Service directory", style = MaterialTheme.typography.titleLarge, fontWeight = FontWeight.SemiBold)
                }
                items(data.services) { service ->
                    PublicServiceCard(service)
                }

                item {
                    ElevatedCard(shape = RoundedCornerShape(24.dp)) {
                        Column(modifier = Modifier.padding(20.dp), verticalArrangement = Arrangement.spacedBy(8.dp)) {
                            Text("Support contacts", style = MaterialTheme.typography.titleMedium, fontWeight = FontWeight.SemiBold)
                            Text(
                                text = "Phone: ${data.settingValue("support_phone") ?: "Not configured"}",
                                color = MaterialTheme.colorScheme.onSurfaceVariant,
                            )
                            Text(
                                text = "Email: ${data.settingValue("support_email") ?: "Not configured"}",
                                color = MaterialTheme.colorScheme.onSurfaceVariant,
                            )
                        }
                    }
                }
            }
        }
    }
}

@Composable
private fun PublicMenuCard(item: CmsMenuItem) {
    ElevatedCard(shape = RoundedCornerShape(24.dp)) {
        Column(modifier = Modifier.padding(20.dp), verticalArrangement = Arrangement.spacedBy(8.dp)) {
            Text(item.title, style = MaterialTheme.typography.titleLarge, fontWeight = FontWeight.SemiBold)
            Text(
                text = "${item.type} ${if (item.openInWebView) "| opens in WebView" else ""}",
                color = MaterialTheme.colorScheme.onSurfaceVariant,
            )
            val target = item.targetReference ?: item.route
            if (!target.isNullOrBlank()) {
                Text(target, style = MaterialTheme.typography.bodySmall, color = MaterialTheme.colorScheme.onSurfaceVariant)
            }
            if (item.children.isNotEmpty()) {
                Text(
                    text = "Children: ${item.children.joinToString { child -> child.title }}",
                    style = MaterialTheme.typography.bodySmall,
                    color = MaterialTheme.colorScheme.onSurfaceVariant,
                )
            }
        }
    }
}

@Composable
private fun PublicContentCard(content: CmsContent) {
    ElevatedCard(shape = RoundedCornerShape(24.dp)) {
        Column(modifier = Modifier.padding(20.dp), verticalArrangement = Arrangement.spacedBy(8.dp)) {
            Text(content.title, style = MaterialTheme.typography.titleLarge, fontWeight = FontWeight.SemiBold)
            Text(
                text = listOfNotNull(content.category, content.contentType, content.audience).joinToString(" | "),
                style = MaterialTheme.typography.bodySmall,
                color = MaterialTheme.colorScheme.onSurfaceVariant,
            )
            Text(content.summary ?: "No summary available.", color = MaterialTheme.colorScheme.onSurfaceVariant)
        }
    }
}

@Composable
private fun PublicFaqCard(faq: CmsFaq) {
    ElevatedCard(shape = RoundedCornerShape(24.dp)) {
        Column(modifier = Modifier.padding(20.dp), verticalArrangement = Arrangement.spacedBy(8.dp)) {
            Text(faq.question, style = MaterialTheme.typography.titleMedium, fontWeight = FontWeight.SemiBold)
            faq.category?.let {
                Text(it, style = MaterialTheme.typography.bodySmall, color = MaterialTheme.colorScheme.onSurfaceVariant)
            }
            Text(faq.answer, color = MaterialTheme.colorScheme.onSurfaceVariant)
        }
    }
}

@Composable
private fun PublicQuizCard(quiz: CmsQuiz) {
    ElevatedCard(shape = RoundedCornerShape(24.dp)) {
        Column(modifier = Modifier.padding(20.dp), verticalArrangement = Arrangement.spacedBy(8.dp)) {
            Text(quiz.title, style = MaterialTheme.typography.titleLarge, fontWeight = FontWeight.SemiBold)
            Text(
                text = "${quiz.questionsCount} questions | ${quiz.audience}",
                style = MaterialTheme.typography.bodySmall,
                color = MaterialTheme.colorScheme.onSurfaceVariant,
            )
            Text(quiz.summary ?: "No summary available.", color = MaterialTheme.colorScheme.onSurfaceVariant)
        }
    }
}

@Composable
private fun PublicServiceCard(service: CmsServiceCenter) {
    ElevatedCard(shape = RoundedCornerShape(24.dp)) {
        Column(modifier = Modifier.padding(20.dp), verticalArrangement = Arrangement.spacedBy(8.dp)) {
            Text(service.name, style = MaterialTheme.typography.titleLarge, fontWeight = FontWeight.SemiBold)
            Text(
                text = buildList {
                    service.district?.let(::add)
                    service.serviceHours?.let(::add)
                    if (service.isFeatured) add("Featured")
                }.joinToString(" | "),
                style = MaterialTheme.typography.bodySmall,
                color = MaterialTheme.colorScheme.onSurfaceVariant,
            )
            Text(service.summary ?: "No summary available.", color = MaterialTheme.colorScheme.onSurfaceVariant)
            Text(
                text = listOfNotNull(service.contactPhone, service.contactEmail).joinToString(" | ").ifBlank { "Contact details pending." },
                style = MaterialTheme.typography.bodySmall,
                color = MaterialTheme.colorScheme.onSurfaceVariant,
            )
        }
    }
}

@OptIn(ExperimentalMaterial3Api::class)
@Composable
private fun PersonSpaceScreen(
    settings: AppSettings,
    authRepository: AuthRepository,
    preferencesRepository: AppPreferencesRepository,
    onBack: () -> Unit,
    onRegister: () -> Unit,
) {
    val scope = rememberCoroutineScope()
    val snackbarHostState = remember { SnackbarHostState() }
    var isRefreshing by rememberSaveable { mutableStateOf(false) }

    Scaffold(
        topBar = {
            TopAppBar(
                title = { Text("Person Space") },
                navigationIcon = {
                    IconButton(onClick = onBack) {
                        Icon(Icons.AutoMirrored.Filled.ArrowBack, contentDescription = "Back")
                    }
                },
                actions = {
                    if (!settings.authToken.isNullOrBlank()) {
                        IconButton(
                            onClick = {
                                isRefreshing = true
                                scope.launch {
                                    val result = authRepository.fetchPermissions(settings.baseUrl, settings.authToken)
                                    result.onSuccess { permissions ->
                                        preferencesRepository.updatePermissions(permissions)
                                        snackbarHostState.showSnackbar("Permissions refreshed.")
                                    }.onFailure { error ->
                                        snackbarHostState.showSnackbar(error.message ?: "Could not refresh permissions.")
                                    }
                                    isRefreshing = false
                                }
                            },
                        ) {
                            Icon(Icons.Filled.Sync, contentDescription = "Refresh permissions")
                        }
                    }
                },
            )
        },
        snackbarHost = { SnackbarHost(hostState = snackbarHostState) },
    ) { paddingValues ->
        if (settings.authToken.isNullOrBlank()) {
            Box(
                modifier = Modifier
                    .fillMaxSize()
                    .padding(paddingValues),
                contentAlignment = Alignment.Center,
            ) {
                Column(
                    modifier = Modifier.padding(24.dp),
                    horizontalAlignment = Alignment.CenterHorizontally,
                    verticalArrangement = Arrangement.spacedBy(12.dp),
                ) {
                    Text("Person Space needs registration.", style = MaterialTheme.typography.headlineSmall, textAlign = TextAlign.Center)
                    Text(
                        "Create an account to pull permission data from the backend and unlock personalized features.",
                        style = MaterialTheme.typography.bodyLarge,
                        color = MaterialTheme.colorScheme.onSurfaceVariant,
                        textAlign = TextAlign.Center,
                    )
                    Button(onClick = onRegister) {
                        Text("Register now")
                    }
                }
            }
            return@Scaffold
        }

        LazyColumn(
            modifier = Modifier
                .fillMaxSize()
                .padding(paddingValues),
            contentPadding = PaddingValues(20.dp),
            verticalArrangement = Arrangement.spacedBy(16.dp),
        ) {
            item {
                ElevatedCard(shape = RoundedCornerShape(28.dp)) {
                    Column(modifier = Modifier.padding(20.dp), verticalArrangement = Arrangement.spacedBy(8.dp)) {
                        Text(settings.personName.orEmpty(), style = MaterialTheme.typography.headlineSmall, fontWeight = FontWeight.Bold)
                        Text(settings.personEmail.orEmpty(), color = MaterialTheme.colorScheme.onSurfaceVariant)
                        Text(
                            text = if (isRefreshing) "Refreshing permission data from ${settings.baseUrl}" else "Permission data comes from ${settings.baseUrl.ifBlank { "your configured backend" }}",
                            color = MaterialTheme.colorScheme.onSurfaceVariant,
                        )
                    }
                }
            }
            item {
                Text("Permissions", style = MaterialTheme.typography.titleLarge, fontWeight = FontWeight.SemiBold)
            }
            item {
                if (settings.permissions.isEmpty()) {
                    ElevatedCard(shape = RoundedCornerShape(22.dp)) {
                        Column(modifier = Modifier.padding(18.dp), verticalArrangement = Arrangement.spacedBy(8.dp)) {
                            Text("No permission data yet.", fontWeight = FontWeight.SemiBold)
                            Text(
                                "The registration flow is ready, but the backend has not returned permissions yet. Use the refresh action once the permissions endpoint is available.",
                                color = MaterialTheme.colorScheme.onSurfaceVariant,
                            )
                        }
                    }
                } else {
                    PermissionGrid(permissions = settings.permissions)
                }
            }
            item {
                OutlinedButton(
                    onClick = {
                        scope.launch {
                            preferencesRepository.clearSession()
                            snackbarHostState.showSnackbar("Person-space session cleared.")
                        }
                    },
                ) {
                    Text("Clear session")
                }
            }
        }
    }
}

@Composable
private fun PermissionGrid(permissions: List<String>) {
    Column(verticalArrangement = Arrangement.spacedBy(10.dp)) {
        permissions.chunked(2).forEach { rowPermissions ->
            Row(horizontalArrangement = Arrangement.spacedBy(10.dp)) {
                rowPermissions.forEach { permission ->
                    AssistChip(onClick = {}, label = { Text(permission) })
                }
            }
        }
    }
}

@OptIn(ExperimentalMaterial3Api::class)
@Composable
private fun SettingsScreen(
    settings: AppSettings,
    preferencesRepository: AppPreferencesRepository,
    navController: NavHostController,
) {
    var baseUrl by rememberSaveable(settings.baseUrl) { mutableStateOf(settings.baseUrl.ifBlank { "http://10.0.2.2:8000" }) }
    var infoMessage by rememberSaveable { mutableStateOf<String?>(null) }
    val scope = rememberCoroutineScope()

    Scaffold(
        topBar = {
            TopAppBar(
                title = { Text("Settings") },
                navigationIcon = {
                    IconButton(onClick = { navController.popBackStack() }) {
                        Icon(Icons.AutoMirrored.Filled.ArrowBack, contentDescription = "Back")
                    }
                },
            )
        },
    ) { paddingValues ->
        LazyColumn(
            modifier = Modifier
                .fillMaxSize()
                .padding(paddingValues),
            contentPadding = PaddingValues(20.dp),
            verticalArrangement = Arrangement.spacedBy(16.dp),
        ) {
            item {
                Text(
                    text = "Configure the backend base URL used for registration and permission syncing. For a local Laravel backend running on the same machine as the emulator, use http://10.0.2.2:8000.",
                    style = MaterialTheme.typography.bodyLarge,
                    color = MaterialTheme.colorScheme.onSurfaceVariant,
                )
            }
            item {
                OutlinedTextField(
                    value = baseUrl,
                    onValueChange = {
                        baseUrl = it
                        infoMessage = null
                    },
                    modifier = Modifier.fillMaxWidth(),
                    label = { Text("API base URL") },
                    singleLine = true,
                )
            }
            item {
                Button(
                    modifier = Modifier.fillMaxWidth(),
                    onClick = {
                        scope.launch {
                            preferencesRepository.saveBaseUrl(baseUrl)
                            infoMessage = "Base URL saved."
                        }
                    },
                ) {
                    Text("Save backend setting")
                }
            }
            item {
                infoMessage?.let {
                    Text(it, color = MaterialTheme.colorScheme.primary)
                }
            }
        }
    }
}