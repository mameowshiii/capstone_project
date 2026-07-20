package com.barangaypili.residentportal

import android.annotation.SuppressLint
import android.app.Activity
import android.graphics.Color
import android.net.http.SslError
import android.os.Bundle
import android.view.Gravity
import android.view.View
import android.webkit.SslErrorHandler
import android.webkit.WebResourceError
import android.webkit.WebResourceRequest
import android.webkit.WebSettings
import android.webkit.WebView
import android.webkit.WebViewClient
import android.widget.Button
import android.widget.FrameLayout
import android.widget.LinearLayout
import android.widget.ProgressBar
import android.widget.TextView

class MainActivity : Activity() {
    private lateinit var webView: WebView
    private lateinit var progressBar: ProgressBar
    private lateinit var errorView: LinearLayout

    @SuppressLint("SetJavaScriptEnabled")
    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)

        val root = FrameLayout(this)
        webView = WebView(this)
        progressBar = ProgressBar(this, null, android.R.attr.progressBarStyleHorizontal)
        errorView = buildErrorView()

        root.addView(webView, FrameLayout.LayoutParams.MATCH_PARENT, FrameLayout.LayoutParams.MATCH_PARENT)
        root.addView(progressBar, FrameLayout.LayoutParams.MATCH_PARENT, 8)
        root.addView(errorView, FrameLayout.LayoutParams.MATCH_PARENT, FrameLayout.LayoutParams.MATCH_PARENT)
        setContentView(root)

        val defaultUa = webView.settings.userAgentString
        webView.settings.apply {
            javaScriptEnabled = true
            domStorageEnabled = true
            databaseEnabled = true
            cacheMode = WebSettings.LOAD_DEFAULT
            mixedContentMode = WebSettings.MIXED_CONTENT_COMPATIBILITY_MODE
            builtInZoomControls = false
            displayZoomControls = false
            loadWithOverviewMode = true
            useWideViewPort = true
            // Custom UA so the server can detect the native WebView and hide the install banner
            userAgentString = "$defaultUa BrgyPiliApp/1.0"
        }

        webView.webViewClient = object : WebViewClient() {
            override fun onPageStarted(view: WebView?, url: String?, favicon: android.graphics.Bitmap?) {
                progressBar.visibility = View.VISIBLE
                errorView.visibility = View.GONE
            }

            override fun onPageFinished(view: WebView?, url: String?) {
                progressBar.visibility = View.GONE
            }

            override fun onReceivedError(
                view: WebView?,
                request: WebResourceRequest?,
                error: WebResourceError?
            ) {
                if (request?.isForMainFrame == true) {
                    showError()
                }
            }

            override fun onReceivedSslError(view: WebView?, handler: SslErrorHandler?, error: SslError?) {
                handler?.cancel()
                showError()
            }
        }

        if (savedInstanceState == null) {
            webView.loadUrl(BuildConfig.PORTAL_URL)
        } else {
            webView.restoreState(savedInstanceState)
        }
    }

    override fun onSaveInstanceState(outState: Bundle) {
        webView.saveState(outState)
        super.onSaveInstanceState(outState)
    }

    override fun onBackPressed() {
        if (webView.canGoBack()) {
            webView.goBack()
        } else {
            super.onBackPressed()
        }
    }

    private fun showError() {
        progressBar.visibility = View.GONE
        errorView.visibility = View.VISIBLE
    }

    private fun buildErrorView(): LinearLayout {
        val container = LinearLayout(this).apply {
            orientation = LinearLayout.VERTICAL
            gravity = Gravity.CENTER
            setPadding(40, 40, 40, 40)
            setBackgroundColor(Color.WHITE)
            visibility = View.GONE
        }

        val title = TextView(this).apply {
            text = getString(R.string.connection_error_title)
            textSize = 20f
            setTextColor(Color.rgb(22, 22, 22))
            gravity = Gravity.CENTER
        }

        val message = TextView(this).apply {
            text = getString(R.string.connection_error_message)
            textSize = 14f
            setTextColor(Color.rgb(107, 114, 128))
            gravity = Gravity.CENTER
            setPadding(0, 12, 0, 24)
        }

        val retry = Button(this).apply {
            text = getString(R.string.retry)
            setOnClickListener {
                errorView.visibility = View.GONE
                webView.reload()
            }
        }

        container.addView(title)
        container.addView(message)
        container.addView(retry)
        return container
    }
}
