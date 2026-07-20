plugins {
    id("com.android.application")
    id("org.jetbrains.kotlin.android")
}

android {
    namespace = "com.barangaypili.residentportal"
    compileSdk = 35

    buildFeatures {
        buildConfig = true
    }

    defaultConfig {
        applicationId = "com.barangaypili.residentportal"
        minSdk = 26
        targetSdk = 35
        versionCode = 1
        versionName = "1.0.0"

        buildConfigField(
            "String",
            "PORTAL_URL",
            "\"https://capstone-project-orpin-theta.vercel.app/resident/my-requests\""
        )
    }
}
