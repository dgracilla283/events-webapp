/*
 * LiquidMetal, version: 0.1 (2009-02-05)
 *
 * A mimetic poly-alloy of Quicksilver's scoring algorithm, essentially
 * LiquidMetal.
 *
 * For usage and examples, visit:
 * http://github.com/rmm5t/liquidmetal
 *
 * Licensed under the MIT:
 * http://www.opensource.org/licenses/mit-license.php
 *
 * Copyright (c) 2009, Ryan McGeary (ryanonjavascript -[at]- mcgeary [*dot*] org)
 */
var LiquidMetal = function() {
  var SCORE_NO_MATCH = 0.0;
  var SCORE_MATCH = 1.0;
  var SCORE_TRAILING = 0.8;
  var SCORE_TRAILING_BUT_STARTED = 0.9;
  var SCORE_BUFFER = 0.85;

  return {
    score: function(string, abbreviation) {
      // Short circuits
      if (abbreviation.length == 0) return SCORE_TRAILING;
      if (abbreviation.length > string.length) return SCORE_NO_MATCH;

      var scores = this.buildScoreArray(string, abbreviation);

      var sum = 0.0;
      for (var i in scores) {
        sum += scores[i];
      }

      return (sum / scores.length);
    },

    buildScoreArray: function(string, abbreviation) {
      var scores = new Array(string.length);
      var lower = string.toLowerCase();
      var chars = abbreviation.toLowerCase().split("");

      var lastIndex = -1;
      var started = false;
      for (var i in chars) {
        var c = chars[i];
        var index = lower.indexOf(c, lastIndex+1);
        if (index < 0) return fillArray(scores, SCORE_NO_MATCH);
        if (index == 0) started = true;

        if (isNewWord(string, index)) {
          scores[index-1] = 1;
          fillArray(scores, SCORE_BUFFER, lastIndex+1, index-1);
        }
        else if (isUpperCase(string, index)) {
          fillArray(scores, SCORE_BUFFER, lastIndex+1, index);
        }
        else {
          fillArray(scores, SCORE_NO_MATCH, lastIndex+1, index);
        }

        scores[index] = SCORE_MATCH;
        lastIndex = index;
      }

      var trailingScore = started ? SCORE_TRAILING_BUT_STARTED : SCORE_TRAILING;
      fillArray(scores, trailingScore, lastIndex+1);
      return scores;
    }
  };

  function isUpperCase(string, index) {
    var c = string.charAt(index);
    return ("A" <= c && c <= "Z");
  }

   function isNewWord(string, index) {
    var c = string.charAt(index-1);
    return (c == " " || c == "\t");
  }

  function fillArray(array, value, from, to) {
    from = Math.max(from || 0, 0);
    to = Math.min(to || array.length, array.length);
    for (var i = from; i < to; i++) { array[i] = value; }
    return array;
  }
}();


// DOM Search (documentation: http://juliocesar.github.com/jquery-domsearch/)
$(function($) {
  function guessUnit(tagName) {
    switch(tagName) {
      case 'TBODY':
      case 'TABLE':
        return 'tr';
      case 'OL':
      case 'UL':
        return 'li';
      case 'DIV':
        return 'div';
    }
    return undefined;
  }

  function search(query, searchIn, options) {
    $($.grep($(searchIn).find(options.unit), function(row) {
      var text;
      switch(options.criteria.constructor) {
        case Array:
          text = $.map(
            options.criteria,
            function(crit) { return $(row).find(crit).text(); }
          ).join(' ');
          break;
        case String:
          text = $(row).find(options.criteria).text();
          break;
        default:
          text = $(row).text();
          break;
      }
      $(row).show().data('domsearch.score', LiquidMetal.score(text, query));
      return $(row).data('domsearch.score') < options.minimumScore;
    })
    .sort(function(a, b) { return $(a).data('domsearch.score') < $(b).data('domsearch.score'); }))
      .appendTo(searchIn)
      .hide();
  }

  function init(element, searchIn, options) {
    var target = $(searchIn),
      defaults = { unit: undefined, criteria: false, minimumScore: 0.5 },
      opts = $.extend(defaults, options);
    opts.unit = opts.unit || guessUnit(target[0].tagName);

    var originalOrder = target.find(opts.unit);

    $(element).keydown(function(event) {
      if (event.keyCode == 9) return true; // TAB
      var field = $(this);
      setTimeout(
        function() {
          if (field.val() == '') {
            originalOrder.show().appendTo(target);
          } else {
            search(field.val(), target[0], opts);
          }
          if (typeof opts.onkeydown == 'function') opts.onkeydown(field);
        },
      100);
      return true;
    });
  }

  $.fn.sort       = function() { return this.pushStack([].sort.apply(this, arguments), []); };
  $.domsearch     = function(element, searchIn, options) { init(element, searchIn, options); };
  $.fn.domsearch  = function(query, options) {
    if (!$(this).data('domsearch.enabled')) {
      $(this).data('domsearch.enabled', true);
      return this.each(function() { new $.domsearch(this, query, options); });
    }
  };
});