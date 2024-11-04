About This Helper Class

This helper class serves as an alternative to laravelcollective/html, allowing you to continue using similar functionality without needing to update your existing Blade templates.

Key Benefits

 - Powerful Functionality: Retains the capabilities of laravelcollective/html.
 - No Template Updates Needed: Works seamlessly with existing Blade templates.

How to Use It in Your Project

1.	Place this class in your project’s helper directory or within a controller path, and make sure it has the correct namespace. For example:

namespace App\Http\Helper;
2. Register it as a facade in your configuration file (config/app.php). Under the aliases section, add:
   'Form' => \App\Http\Helper\FormHelper::class,
   
This will allow you to use the helper class as a replacement for laravelcollective/html with minimal adjustments.

Thank you for using this custom class.

Adel Abou Elez
