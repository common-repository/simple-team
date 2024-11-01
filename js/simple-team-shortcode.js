jQuery(document).ready(function ($) {


    tinymce.PluginManager.add('simple_team_button', function (editor, url) {
        editor.addButton('simple_team_button', {
            title: 'Add Team Snippet',
            icon: 'icon dashicons-groups',
            onclick: function () {
                editor.windowManager.open({
                    title: 'Team',
                    body: [
                        {type: 'textbox', name: 'title', label: 'Titel', value: 'Our Team'},
                        {type: 'textbox', name: 'subtitle', label: 'Sub Title', value: 'We have a perfect Team'},
                        {type: 'textbox', name: 'extra_class', label: 'Extra CSS Class', value: ''},
                        {type: 'textbox', name: 'limit', label: 'Maximum Portfolios', value: '6'},
                        {type: 'listbox', name: 'id', label: 'Team Category', 'values': shortcode_team_feeds},
                        {type: 'listbox', name: 'template', label: 'Template', 'values': shortcode_team_templates}
                    ],
                    onsubmit: function (e) {
                        editor.insertContent('[simple-team id=' + e.data.id + ' extra_class="' + e.data.extra_class + '" template="' + e.data.template + '" limit="' + e.data.limit + '" title="' + e.data.title + '"  subtitle="' + e.data.subtitle + '"/]');
                    }
                });
            }
        });
    });

});
  