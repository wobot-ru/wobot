<!doctype html>
<html>
  <head>
    <title>CodeMirror: Clojure mode</title>
    <link rel="stylesheet" href="codemirror/lib/codemirror.css">
    <script src="codemirror/lib/codemirror.js"></script>
    <script src="codemirror/mode/clojure/clojure.js"></script>
    <style>.CodeMirror {background: #f8f8f8;}</style>
    <link rel="stylesheet" href="codemirror/doc/docs.css">
  </head>
  <body>
    <h1>CodeMirror: Clojure mode</h1>
    <form><textarea id="code" name="code">

</textarea></form>
    <script>
      var editor = CodeMirror.fromTextArea(document.getElementById("code"), {});
    </script>

  </body>
</html>
