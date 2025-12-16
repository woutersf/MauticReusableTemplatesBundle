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

        // Fetch templates list from API and register blocks
        async function loadAndRegisterTemplates() {
            try {
                const response = await fetch(window.location.origin + '/reusabletemplates/api/list', {
                    method: 'GET',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    credentials: 'same-origin'
                });

                const data = await response.json();

                if (data.success && data.templates && data.templates.length > 0) {
                    console.log('Reusable Templates: Found', data.templates.length, 'templates');

                    // Store templates for later use
                    availableTemplates = data.templates;

                    // Register a block for each template
                    data.templates.forEach(function(template) {
                        registerTemplateBlock(template);
                    });
                } else {
                    console.log('Reusable Templates: No templates found');
                    availableTemplates = [];
                }
            } catch (error) {
                console.error('Reusable Templates: Error loading templates:', error);
            }
        }

        // Register a single template block
        function registerTemplateBlock(template) {
            const blockId = 'reusable-template-' + template.id;

            // Add block to BlockManager
            editor.BlockManager.add(blockId, {
                label: template.name,
                category: 'Reusable Templates',
                content: template.content || '',
                media: '<i class="fa fa-puzzle-piece" style="font-size: 32px; color: #486AE2;"></i>',
                attributes: {
                    title: 'Insert ' + template.name,
                    class: 'reusable-template-block'
                }
            });

            console.log('Reusable Templates: Registered block for', template.name);
        }

        // Load templates when editor is ready
        editor.on('load', function() {
            console.log('Reusable Templates: Editor loaded, fetching templates...');
            loadAndRegisterTemplates();
        });
    };

    // Register plugin with Mautic GrapesJS
    if (!window.MauticGrapesJsPlugins) {
        window.MauticGrapesJsPlugins = [];
    }

    window.MauticGrapesJsPlugins.push({
        name: 'mautic-reusabletemplates',
        plugin: reusableTemplatesPlugin
    });

    console.log('Reusable Templates: Plugin registered with Mautic GrapesJS');
})();
