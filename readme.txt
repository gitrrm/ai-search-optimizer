=== AI Search Optimizer ===
Contributors: gitrm
Tags: ai search, seo, aeo, geo, ai visibility, technical seo, wordpress
Requires at least: 6.0
Requires PHP: 7.4
Stable tag: 0.1.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

AI Search Optimizer helps WordPress site owners analyze technical signals that can influence search engine and AI/LLM discoverability.

== Description ==

AI Search Optimizer is a WordPress plugin focused on Technical SEO, Answer Engine Optimization (AEO), and Generative Engine Optimization (GEO).

The plugin analyzes selected website signals and provides actionable information to help improve how content and site metadata can be understood by traditional search engines and AI-powered search systems.

Current analysis areas include:

* Structured data and Schema detection
* FAQ content and FAQ Schema detection
* Open Graph metadata
* Robots directives
* llms.txt detection
* AI/search visibility analysis
* Actionable optimization recommendations

The project is actively under development.

== Current Features ==

= Technical SEO & AI Search Analysis =

The analyzer currently includes detectors for:

* Schema / structured data
* FAQ content
* FAQ Schema
* Open Graph metadata
* Robots directives
* llms.txt

= Admin Dashboard =

The plugin provides an administrative dashboard for viewing analysis results and AI-search-related recommendations.

== Requirements ==

* WordPress 6.0 or later
* PHP 7.4 or later
* Composer for development

== Installation ==

= Development Installation =

Clone the repository:

git clone https://github.com/gitrm/ai-search-optimizer.git

Change to the plugin directory:

cd ai-search-optimizer

Install Composer dependencies:

composer install

Copy the plugin into the WordPress plugins directory, or use the project directly within a local WordPress development environment.

Activate the plugin from:

WordPress Admin → Plugins → Installed Plugins

== Development ==

Install dependencies:

composer install

Regenerate the Composer autoloader after namespace or class changes:

composer dump-autoload

Run PHPUnit tests:

vendor/bin/phpunit

Run WordPress Coding Standards checks:

vendor/bin/phpcs

== Architecture ==

The plugin uses PSR-4 autoloading with the ASO namespace.

src/

* Admin/ - WordPress administration and dashboard functionality
* Core/ - Plugin bootstrap and core services
* SEO/ - SEO and AI-search detection components
* Services/ - Analysis and application services

tests/

Contains automated tests for plugin functionality.

== Roadmap ==

Planned improvements include:

* AI Visibility Score
* Expanded GEO analysis
* Expanded AEO analysis
* Entity and brand signals
* Content quality and citation-readiness analysis
* Additional structured-data validation
* More actionable optimization recommendations
* Improved dashboard reporting
* Automated technical SEO checks
* Additional automated tests
* Support for additional AI-search signals

== Privacy ==

AI Search Optimizer is designed to analyze website content and technical signals available within the WordPress installation.

The plugin does not require an external AI API for its core analysis.

== Support ==

For development and project information, visit:

https://github.com/gitrm/ai-search-optimizer

== Changelog ==

= 0.1.0 =
* Initial development release.
* Added PSR-4 Composer autoloading.
* Added technical SEO and AI-search detectors.
* Added initial administration dashboard.