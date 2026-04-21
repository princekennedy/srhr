package com.example.myapplication.model

data class AppBootstrap(
    val menuTitle: String? = null,
    val menuItems: List<CmsMenuItem> = emptyList(),
    val categories: List<CmsCategory> = emptyList(),
    val featuredContents: List<CmsContent> = emptyList(),
    val faqs: List<CmsFaq> = emptyList(),
    val quizzes: List<CmsQuiz> = emptyList(),
    val services: List<CmsServiceCenter> = emptyList(),
    val settings: List<PublicSetting> = emptyList(),
) {
    fun settingValue(key: String): String? = settings.firstOrNull { it.key == key }?.value
}

data class CmsMenuItem(
    val title: String,
    val type: String,
    val icon: String?,
    val targetReference: String?,
    val route: String?,
    val openInWebView: Boolean,
    val children: List<CmsMenuItem> = emptyList(),
)

data class CmsCategory(
    val name: String,
    val description: String?,
    val contentsCount: Int,
)

data class CmsContent(
    val title: String,
    val summary: String?,
    val contentType: String,
    val audience: String,
    val category: String?,
)

data class CmsFaq(
    val question: String,
    val answer: String,
    val category: String?,
)

data class CmsQuiz(
    val title: String,
    val summary: String?,
    val questionsCount: Int,
    val audience: String,
)

data class CmsServiceCenter(
    val name: String,
    val district: String?,
    val summary: String?,
    val serviceHours: String?,
    val contactPhone: String?,
    val contactEmail: String?,
    val isFeatured: Boolean,
)

data class PublicSetting(
    val key: String,
    val label: String,
    val value: String,
    val group: String,
)