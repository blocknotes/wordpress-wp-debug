window.onload = function() {
	var wpd_js = document.getElementById( 'wpd-js' );
	if( wpd_js !== null )
	{
		var js_ed = CodeMirror.fromTextArea( wpd_js, { lineNumbers: true, mode: 'javascript' } );
		js_ed.addKeyMap( { 'Shift-Enter': function( cm ) { document.getElementById('form-wpd-js').submit(); } } );
	}
	var wpd_php = document.getElementById( 'wpd-php' );
	if( wpd_php !== null )
	{
		var php_ed = CodeMirror.fromTextArea( wpd_php, { lineNumbers: true, mode: 'text/x-php' } );
		php_ed.addKeyMap( { 'Shift-Enter': function( cm ) { document.getElementById('form-wpd-php').submit(); } } );
	}
	var wpd_query = document.getElementById( 'wpd-query' );
	if( wpd_query !== null )
	{
		var sql_ed = CodeMirror.fromTextArea( wpd_query, { lineNumbers: true, mode: 'text/x-sql' } );
		sql_ed.addKeyMap( { 'Shift-Enter': function( cm ) { document.getElementById('form-wpd-query').submit(); } } );
	}
};

//jQuery(document).ready(function($){
//});
