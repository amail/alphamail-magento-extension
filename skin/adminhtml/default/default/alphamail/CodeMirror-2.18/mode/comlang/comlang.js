(function() {
  function keywords(str) {
    var obj = {}, words = str.split(" ");
    for (var i = 0; i < words.length; ++i) obj[words[i]] = true;
    return obj;
  }
  function heredoc(delim) {
    return function(stream, state) {
      if (stream.match(delim)) state.tokenize = null;
      else stream.skipToEnd();
      return "string";
    }
  }
  var comlangConfig = {
    name: "clike",
    keywords: keywords("else if while"),
    blockKeywords: keywords("else if while"),
    atoms: keywords("true false null"),
    multiLineStrings: false,
    hooks: {
      "<": function(stream, state) {
        if (stream.match(/<</)) {
          stream.eatWhile(/[\w\.]/);
          state.tokenize = heredoc(stream.current().slice(3));
          return state.tokenize(stream, state);
        }
        return false;
      }
    }
  };

  CodeMirror.defineMode("comlang", function(config, parserConfig) {
    var htmlMode = CodeMirror.getMode(config, "text/html");
    var jsMode = CodeMirror.getMode(config, "text/javascript");
    var cssMode = CodeMirror.getMode(config, "text/css");
    var comlangMode = CodeMirror.getMode(config, comlangConfig);

    function dispatch(stream, state) { // TODO open comlang inside text/css
      if (state.curMode == htmlMode) {
        var style = htmlMode.token(stream, state.curState);
        if (style == "meta" && /^<\#/.test(stream.current())) {
          state.curMode = comlangMode;
          state.curState = state.comlang;
          state.curClose = /^\#>/;
		  state.mode =  'comlang';
        }
        else if (style == "tag" && stream.current() == ">" && state.curState.context) {
          if (/^script$/i.test(state.curState.context.tagName)) {
            state.curMode = jsMode;
            state.curState = jsMode.startState(htmlMode.indent(state.curState, ""));
            state.curClose = /^<\/\s*script\s*>/i;
			state.mode =  'javascript';
          }
          else if (/^style$/i.test(state.curState.context.tagName)) {
            state.curMode = cssMode;
            state.curState = cssMode.startState(htmlMode.indent(state.curState, ""));
            state.curClose =  /^<\/\s*style\s*>/i;
            state.mode =  'css';
          }
        }
        return style;
      }
      else if (stream.match(state.curClose, false)) {
        state.curMode = htmlMode;
        state.curState = state.html;
        state.curClose = null;
		state.mode =  'html';
        return dispatch(stream, state);
      }
      else return state.curMode.token(stream, state.curState);
    }

    return {
      startState: function() {
        var html = htmlMode.startState();
        return {html: html,
                comlang: comlangMode.startState(),
                curMode:	parserConfig.startOpen ? comlangMode : htmlMode,
                curState:	parserConfig.startOpen ? comlangMode.startState() : html,
                curClose:	parserConfig.startOpen ? /^\#>/ : null,
				mode:		parserConfig.startOpen ? 'comlang' : 'html'}
      },

      copyState: function(state) {
        var html = state.html, htmlNew = CodeMirror.copyState(htmlMode, html),
            comlang = state.comlang, comlangNew = CodeMirror.copyState(comlangMode, comlang), cur;
        if (state.curState == html) cur = htmlNew;
        else if (state.curState == comlang) cur = comlangNew;
        else cur = CodeMirror.copyState(state.curMode, state.curState);
        return {html: htmlNew, comlang: comlangNew, curMode: state.curMode, curState: cur, curClose: state.curClose};
      },

      token: dispatch,

      indent: function(state, textAfter) {
        if ((state.curMode != comlangMode && /^\s*<\//.test(textAfter)) ||
            (state.curMode == comlangMode && /^\#>/.test(textAfter)))
          return htmlMode.indent(state.html, textAfter);
        return state.curMode.indent(state.curState, textAfter);
      },

      electricChars: "/{}:"
    }
  });
  CodeMirror.defineMIME("application/x-httpd-comlang", "comlang");
  CodeMirror.defineMIME("application/x-httpd-comlang-open", {name: "comlang", startOpen: true});
  CodeMirror.defineMIME("text/x-comlang", comlangConfig);
})();
