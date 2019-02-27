define("ace/mode/template_highlight_rules", ["require", "exports", "module", "ace/lib/oop", "ace/mode/html_highlight_rules", "ace/mode/css_highlight_rules"], function(require, exports, module) {
    "use strict";
    var oop = require("../lib/oop"),
        HtmlHighlightRules = require("./html_highlight_rules").HtmlHighlightRules,
        TemplateHighlightRules = function() {
            HtmlHighlightRules.call(this);
            var rules = [{
                token: "markup.bold",
                regex: /\[\[[A-Z0-9_]+\]\]/
            }, {
                token: "markup.bold",
                regex: /<!--\s+(BEGIN|END)\s+[a-z0-9_-]+\s+-->/
            }];
            for (var key in this.$rules) {
                this.$rules[key].unshift.apply(this.$rules[key], rules);
            }
            this.normalizeRules();
        };
    oop.inherits(TemplateHighlightRules, HtmlHighlightRules);
    exports.TemplateHighlightRules = TemplateHighlightRules;
}), define("ace/mode/template", ["require", "exports", "module", "ace/lib/oop", "ace/mode/html","ace/mode/javascript","ace/mode/css", "ace/mode/template_highlight_rules"], function(require, exports, module) {
    "use strict";
    var oop = require("../lib/oop"),
        HtmlMode = require("./html").Mode,
        TemplateHighlightRules = require("./template_highlight_rules").TemplateHighlightRules,
        JavaScriptMode = require("./javascript").Mode,
        CssMode = require("./css").Mode,
        WorkerClient = require("../worker/worker_client").WorkerClient;

    var Mode = function() {
        HtmlMode.call(this);
        this.HighlightRules = TemplateHighlightRules;
        this.createModeDelegates({
            "js-": JavaScriptMode,
            "css-": CssMode
        });
    };

    oop.inherits(Mode, HtmlMode);
    (function() {
        this.createWorker = function(session) {
            if (this.constructor != Mode)
                return;
            var worker = new WorkerClient(["ace"], "ace/mode/template_worker", "Worker");
            worker.attachToDocument(session.getDocument());

            if (this.fragmentContext)
                worker.call("setOptions", [{context: this.fragmentContext}]);

            worker.on("error", function(e) {
                session.setAnnotations(e.data);
            });

            worker.on("terminate", function() {
                session.clearAnnotations();
            });

            return worker;
        };
        this.$id = "ace/mode/template"
    }).call(Mode.prototype);

    exports.Mode = Mode;
});