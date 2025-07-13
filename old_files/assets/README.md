# Assets Directory

This directory contains all the static assets for the HR Management System dashboard.

## Directory Structure

```
assets/
├── css/
│   ├── icons/
│   │   └── tabler-icons/
│   │       ├── fonts/
│   │       └── tabler-icons.min.css
│   ├── styles.css
│   └── styles.min.css
├── js/
│   ├── app.min.js
│   ├── dashboard.js
│   └── sidebarmenu.js
├── libs/
│   ├── apexcharts/
│   │   └── dist/
│   │       ├── apexcharts.min.js
│   │       └── apexcharts.css
│   ├── bootstrap/
│   │   └── dist/
│   │       ├── css/
│   │       └── js/
│   ├── jquery/
│   │   └── dist/
│   │       └── jquery.min.js
│   └── simplebar/
│       └── dist/
│           ├── simplebar.min.js
│           └── simplebar.css
├── images/
│   ├── logos/
│   │   ├── dark-logo.svg
│   │   └── favicon.png
│   └── default.png
├── scss/
└── styles.min.css
```

## Key Features

### CSS Framework
- **Bootstrap 5.3.0**: Modern responsive CSS framework
- **Custom Styles**: Tailored design system with HR-specific components
- **Tabler Icons**: Professional icon set for the interface

### JavaScript Libraries
- **jQuery**: DOM manipulation and AJAX requests
- **Bootstrap JS**: Interactive components (dropdowns, modals, etc.)
- **ApexCharts**: Interactive charts and graphs for analytics
- **SimpleBar**: Custom scrollbars for better UX

### Charts and Analytics
The dashboard includes:
- Employee status bar chart
- Status distribution pie chart
- Real-time data visualization
- Responsive chart containers

### Responsive Design
- Mobile-first approach
- Tablet and desktop optimizations
- Touch-friendly interface
- Adaptive layouts

## Setup Instructions

### 1. File Permissions
Ensure the assets directory has proper read permissions:
```bash
chmod -R 755 assets/
```

### 2. Web Server Configuration
Make sure your web server is configured to serve static files from the assets directory.

### 3. CDN Fallbacks
The system uses CDN for Font Awesome icons. If you prefer local hosting:
1. Download Font Awesome
2. Place files in `assets/libs/fontawesome/`
3. Update the CSS link in dashboard files

### 4. Customization

#### Colors
The color scheme is defined in CSS variables:
```css
:root {
  --primary-color: #0085db;
  --secondary-color: #707a82;
  --success-color: #4bd08b;
  --warning-color: #f8c076;
  --danger-color: #c50000;
  --info-color: #46caeb;
}
```

#### Logo
Replace `assets/images/logos/dark-logo.svg` with your company logo.

#### Favicon
Replace `assets/images/logos/favicon.png` with your favicon (16x16, 32x32, or 48x48 pixels).

## Browser Support

- Chrome 90+
- Firefox 88+
- Safari 14+
- Edge 90+

## Performance Optimization

### Minified Files
- Use `styles.min.css` and `app.min.js` in production
- Unminified versions available for development

### Image Optimization
- SVG logos for scalability
- Optimize PNG images for web
- Consider WebP format for better compression

### Caching
Set appropriate cache headers for static assets:
```apache
# .htaccess
<FilesMatch "\.(css|js|png|jpg|jpeg|gif|ico|svg)$">
    ExpiresActive On
    ExpiresDefault "access plus 1 month"
</FilesMatch>
```

## Troubleshooting

### Charts Not Loading
1. Check if ApexCharts library is loaded
2. Verify chart container IDs exist
3. Check browser console for JavaScript errors

### Icons Not Displaying
1. Verify Tabler Icons CSS is loaded
2. Check Font Awesome CDN availability
3. Ensure icon class names are correct

### Responsive Issues
1. Check viewport meta tag
2. Verify Bootstrap CSS is loaded
3. Test on different screen sizes

## Development

### Adding New Charts
1. Include ApexCharts library
2. Create chart container with unique ID
3. Initialize chart with options
4. Add responsive breakpoints

### Custom Components
1. Add CSS in `styles.css`
2. Include JavaScript in appropriate file
3. Test across different browsers
4. Ensure accessibility compliance

## Security Notes

- All assets are served as static files
- No sensitive data in client-side code
- Validate all user inputs
- Use HTTPS in production

## Version History

- **v1.0**: Initial dashboard implementation
- **v1.1**: Added charts and analytics
- **v1.2**: Improved responsive design
- **v1.3**: Enhanced accessibility and performance

---

For support or questions, please refer to the main project documentation.