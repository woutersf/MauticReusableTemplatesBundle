# TODO - Reusable Email Parts

## Features to Implement

### 1. Template Preview/Rendering
- [ ] Add template rendering section to show preview of the email part while creating/editing
- [ ] Display HTML content in a preview panel
- [ ] Add live preview functionality
- [ ] Support for dynamic content rendering

### 2. Email Content Synchronization
- [x] Add "changed" column to track template modifications
- [x] Set changed flag to 1 when template is created/updated
- [x] Create cronjob to process changed templates
- [ ] Implement logic to find all emails using this template
- [ ] Replace old template content with new content in emails
- [ ] Add batch processing for large numbers of emails
- [ ] Add logging for template replacements

### 3. GrapeJS Integration
- [x] Add Reusable Templates section to GrapeJS editor
- [x] Display buttons for each template in the database
- [ ] Add drag-and-drop functionality for templates
- [ ] Support for template variables/placeholders
- [ ] Add template categories/organization

### 4. Future Enhancements
- [ ] Add template versioning
- [ ] Add template usage statistics (which emails use which templates)
- [ ] Add template search/filter in GrapeJS
- [ ] Add template categories/tags
- [ ] Add template import/export functionality
- [ ] Add permission management for template access
- [ ] Add template cloning functionality
