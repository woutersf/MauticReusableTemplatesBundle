/**
 * GrapesJS Reusable Templates Plugin
 * Dynamically creates one block per reusable template from database
 */
(function() {
    'use strict';

    console.log('Reusable Templates: Script loaded, initializing...');

    // Reusable Templates Plugin for GrapesJS
    const reusableTemplatesPlugin = function(editor, opts = {}) {
        let availableTemplates = [];
        let isLoaded = false;

        // Fetch templates list from API and register blocks
        async function loadAndRegisterTemplates() {
            if (isLoaded) {
                console.log('Reusable Templates: Already loaded');
                return;
            }

            console.log('Reusable Templates: Fetching templates from API...');

            try {
                // Use absolute path from window origin (no /s/ prefix for public routes)
                const fetchUrl = window.location.origin + '/reusabletemplates/list';
                console.log('Reusable Templates: Fetching from:', fetchUrl);

                const response = await fetch(fetchUrl, {
                    method: 'GET',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    credentials: 'same-origin'
                });

                console.log('Reusable Templates: Response status:', response.status);

                if (!response.ok) {
                    console.error('Reusable Templates: HTTP error', response.status, response.statusText);
                    return;
                }

                const data = await response.json();
                console.log('Reusable Templates: Received data:', data);

                if (data.success && data.templates && data.templates.length > 0) {
                    console.log('Reusable Templates: Found', data.templates.length, 'templates');

                    // Store templates for later use
                    availableTemplates = data.templates;

                    // Register a block for each template
                    data.templates.forEach(function(template) {
                        try {
                            registerTemplateBlock(template);
                        } catch (blockError) {
                            console.error('Reusable Templates: Error registering block for template', template.id, blockError);
                        }
                    });

                    isLoaded = true;
                    console.log('Reusable Templates: All blocks registered successfully');
                } else {
                    console.log('Reusable Templates: No templates found or success=false');
                    availableTemplates = [];
                }
            } catch (error) {
                console.error('Reusable Templates: Error loading templates:', error);
                console.error('Reusable Templates: Error stack:', error.stack);
            }
        }

        // Register a single template block
        function registerTemplateBlock(template) {
            if (!template || !template.id) {
                console.error('Reusable Templates: Invalid template object', template);
                return;
            }

            const blockId = 'reusable-template-' + template.id;
            console.log('Reusable Templates: Registering block:', blockId, 'for template:', template.name);

            try {
                // Check if BlockManager exists
                if (!editor.BlockManager) {
                    console.error('Reusable Templates: BlockManager not available');
                    return;
                }

                // Add block to BlockManager
                editor.BlockManager.add(blockId, {
                    label: template.name || 'Unnamed Template',
                    category: 'Reusable Templates',
                    content: template.content || '<p>Empty template</p>',
                    media: '<i class="fa fa-puzzle-piece" style="font-size: 32px; color: #486AE2;"></i>',
                    attributes: {
                        title: 'Insert ' + (template.name || 'template'),
                        class: 'reusable-template-block'
                    }
                });

                console.log('Reusable Templates: Successfully registered block for', template.name);
            } catch (error) {
                console.error('Reusable Templates: Error in registerTemplateBlock:', error);
            }
        }

        // Initialize when editor is ready
        console.log('Reusable Templates: Setting up editor event listeners...');

        // Try multiple events to ensure initialization
        editor.on('load', function() {
            console.log('Reusable Templates: Editor "load" event fired');
            setTimeout(function() {
                loadAndRegisterTemplates();
            }, 100);
        });

        // Also try on storage:load in case load already fired
        editor.on('storage:load', function() {
            console.log('Reusable Templates: Editor "storage:load" event fired');
            if (!isLoaded) {
                setTimeout(function() {
                    loadAndRegisterTemplates();
                }, 100);
            }
        });

        // Immediate attempt if editor is already loaded
        if (editor.getWrapper()) {
            console.log('Reusable Templates: Editor already loaded, fetching immediately');
            setTimeout(function() {
                loadAndRegisterTemplates();
            }, 500);
        }
    };

    // Register plugin with Mautic GrapesJS
    if (!window.MauticGrapesJsPlugins) {
        console.log('Reusable Templates: Creating MauticGrapesJsPlugins array');
        window.MauticGrapesJsPlugins = [];
    }

    window.MauticGrapesJsPlugins.push({
        name: 'mautic-reusabletemplates',
        plugin: reusableTemplatesPlugin
    });

    console.log('Reusable Templates: Plugin registered with Mautic GrapesJS');
    console.log('Reusable Templates: Total plugins registered:', window.MauticGrapesJsPlugins.length);
})();
