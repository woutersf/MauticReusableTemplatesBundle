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
- [x] Implement logic to find all emails using this template
- [x] Replace old template content with new content in emails
- [ ] Add batch processing for large numbers of emails
- [ ] Add logging for template replacements

### 2.1. CRITICAL: MJML to HTML Conversion for Apply Functionality
**Problem:**
When clicking "Apply" on `/s/reusabletemplates/apply/{id}`, the system currently:
- ✅ Updates `bundle_grapesjsbuilder.custom_mjml` with new template content
- ❌ Does NOT update `emails.custom_html` (the compiled HTML used for sending emails)
- ⚠️ Result: Emails continue sending with OLD content until manually opened/saved in GrapeJS

**Current Implementation:**
- File: `Controller/TemplateController.php`, method `applyProcessAction()` (lines 391-426)
- Uses DOMDocument to find elements with `data-reusablesectionid="X"` in MJML
- Replaces matching elements with new template content
- Updates only the MJML source in database

**Required Fix:**
Implement server-side MJML to HTML conversion after updating the MJML source.

**Solution Options:**

1. **PHP MJML Library** (Recommended)
   - Use a PHP package like `spatie/mjml-php` or similar
   - Install via composer: `composer require spatie/mjml-php`
   - After updating `custom_mjml`, convert to HTML and update `emails.custom_html`

2. **MJML API** (Alternative)
   - Call MJML.io API: https://mjml.io/api
   - Requires API key and external dependency
   - May have rate limits

3. **Node.js CLI** (Alternative)
   - Execute `mjml` CLI command via PHP `exec()`
   - Requires Node.js installed on server
   - Example: `exec('echo "' . $mjml . '" | mjml', $output)`

**Implementation Steps:**
1. Choose and install MJML conversion method
2. In `applyProcessAction()`, after line 422 (after updating `custom_mjml`):
   ```php
   // Convert MJML to HTML
   $compiledHtml = convertMjmlToHtml($updatedMjml);

   // Update the emails table with compiled HTML
   $updateHtmlSql = "UPDATE emails SET custom_html = :html WHERE id = :id";
   $updateHtmlStmt = $connection->prepare($updateHtmlSql);
   $updateHtmlStmt->bindValue('html', $compiledHtml);
   $updateHtmlStmt->bindValue('id', $email['id']);
   $updateHtmlStmt->executeStatement();
   ```
3. Test with multiple emails to ensure conversion works correctly
4. Add error handling for conversion failures
5. Consider adding a flash message if conversion fails

**Files to Modify:**
- `Controller/TemplateController.php` - Add MJML to HTML conversion
- `composer.json` - Add MJML library dependency (if using PHP option)

**Testing:**
1. Create a reusable template
2. Insert it into multiple test emails via GrapeJS
3. Modify the template content
4. Click "Apply" on the apply page
5. Verify `custom_mjml` is updated in `bundle_grapesjsbuilder`
6. Verify `custom_html` is updated in `emails` table
7. Send test emails to confirm new content is used

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
