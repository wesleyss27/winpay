(window.webpackJsonp = window.webpackJsonp || []).push([
    [1],
    [
        ,
        ,
        ,
        ,
        ,
        ,
        ,
        ,
        ,
        ,
        ,
        ,
        ,
        ,
        function (e, t, n) {
            "use strict";
            t.a = {
                black: "#12263f",
                white: "#ffffff",
                gray: { 300: "#e3ebf6", 600: "#95aac9", 700: "#6e84a3", 800: "#152e4d", 900: "#283e59" },
                primary: { 100: "#d2ddec", 300: "#a6c5f7", 700: "#2c7be5" },
                set: function (e, t) {
                    var n = arguments.length > 2 && void 0 !== arguments[2] ? arguments[2] : {},
                        r = n.alpha;
                    if (r) {
                        var a = [t.r, t.g, t.b].join(",");
                        t = "rgba(".concat(a, ", ").concat(r, ")");
                    }
                    return document.documentElement.style.setProperty("--".concat(e), t);
                },
                get: function (e, t) {
                    var n = getComputedStyle(document.documentElement).getPropertyValue("--".concat(e));
                    return (null === n || "" === n ? t : n).replace(/^\s+/, "").replace(/\s+$/, "");
                },
                unset: function (e) {
                    return document.documentElement.style.removeProperty("--".concat(e));
                },
            };
        },
        ,
        ,
        ,
        ,
        ,
        ,
        ,
        function (e, t, n) {
            "use strict";
            var r = n(34),
                a = n.n(r),
                i = n(96),
                o = n(73);
            t.a = i.a.extend({
                timeout: !1,
                throwHttpErrors: !1,
                hooks: {
                    beforeRequest: [
                        function (e) {
                            e.headers.set("X-CSRF-Token", a.a.csrfToken()), o.b.show();
                        },
                    ],
                    afterResponse: [
                        function () {
                            o.b.hide();
                        },
                    ],
                },
            });
        },
        ,
        ,
        ,
        ,
        ,
        ,
        ,
        ,
        ,
        ,
        ,
        ,
        ,
        function (e, t, n) {
            "use strict";
            function r(e) {
                var t = document.getElementById(e);
                return null === t ? "" : "checkbox" === t.type ? t.checked : t.value;
            }
            n.d(t, "a", function () {
                return r;
            });
        },
        ,
        ,
        ,
        ,
        ,
        ,
        ,
        ,
        ,
        ,
        function (e, t, n) {
            "use strict";
            var r = n(125),
                a = n.n(r);
            t.a = function (e) {
                return (a.a + "#".concat(e)).replace(/.*(\/packs)/, "$1");
            };
        },
        ,
        ,
        ,
        ,
        ,
        ,
        ,
        ,
        ,
        ,
        ,
        ,
        ,
        ,
        ,
        ,
        ,
        ,
        ,
        ,
        ,
        ,
        ,
        ,
        ,
        function (e, t, n) {
            "use strict";
            n.d(t, "b", function () {
                return i;
            });
            var r = n(37),
                a = n.n(r);
            a.a.SnapshotRenderer.prototype.assignNewBody = function () {
                var e = document.body.querySelector(".mount"),
                    t = this.newBody.querySelector(".mount");
                e && t ? e.replaceWith(t) : (document.body = this.newBody);
            };
            var i = {
                show: function () {
                    a.a.controller.adapter.progressBar.setValue(0), a.a.controller.adapter.progressBar.show();
                },
                hide: function () {
                    a.a.controller.adapter.progressBar.setValue(100), a.a.controller.adapter.progressBar.hide();
                },
            };
            t.a = {
                _handleAjaxBefore: function () {
                    i.show();
                },
                _handleAjaxComplete: function () {
                    i.hide();
                },
                _handlePartials: function (e) {
                    var t = e.detail[0],
                        n = t.responseURL;
                    if (!t.getResponseHeader("X-Turbolinks-Partial") && "text/html" === (t.getResponseHeader("Content-Type") || "").substring(0, 9)) {
                        var r = a.a.Snapshot.wrap(t.response);
                        a.a.controller.cache.put(n, r), a.a.visit(n, { action: "restore" });
                    }
                },
                start: function () {
                    a.a.start(),
                        a.a.setProgressBarDelay(100),
                        document.addEventListener("ajax:before", this._handleAjaxBefore, !1),
                        document.addEventListener("ajax:error", this._handleAjaxComplete, !1),
                        document.addEventListener("ajax:complete", this._handleAjaxComplete, !0),
                        document.addEventListener("ajax:complete", this._handlePartials, !1);
                },
            };
        },
        ,
        ,
        ,
        ,
        ,
        ,
        ,
        ,
        ,
        ,
        ,
        ,
        ,
        ,
        ,
        ,
        ,
        ,
        ,
        ,
        ,
        ,
        ,
        function (e, t, n) {
            "use strict";
            var r = n(42),
                a = n(14);
            t.a = {
                start: function () {
                    var e = {};
                    return (
                        "pt-BR" === document.documentElement.lang &&
                            (e = {
                                months: ["Janeiro", "Fevereiro", "Mar\xe7o", "Abril", "Maio", "Junho", "Julho", "Agosto", "Setembro", "Outubro", "Novembro", "Dezembro"],
                                shortMonths: ["Jan", "Fev", "Mar", "Abr", "Mai", "Jun", "Jul", "Ago", "Set", "Out", "Nov", "Dez"],
                                weekdays: ["Domingo", "Segunda", "Ter\xe7a", "Quarta", "Quinta", "Sexta", "S\xe1bado"],
                                loading: ["Atualizando o gr\xe1fico...aguarde"],
                                contextButtonTitle: "Exportar gr\xe1fico",
                                decimalPoint: ",",
                                thousandsSep: ".",
                                downloadJPEG: "Baixar imagem JPEG",
                                downloadPDF: "Baixar arquivo PDF",
                                downloadPNG: "Baixar imagem PNG",
                                downloadSVG: "Baixar vetor SVG",
                                printChart: "Imprimir gr\xe1fico",
                                rangeSelectorFrom: "De",
                                rangeSelectorTo: "Para",
                                rangeSelectorZoom: "Zoom",
                                resetZoom: "Limpar Zoom",
                                resetZoomTitle: "Voltar Zoom para n\xedvel 1:1",
                            }),
                        Highcharts.setOptions({
                            lang: e,
                            chart: { backgroundColor: a.a.black, borderRadius: 0, height: 300, style: { fontFamily: "Cerebri Sans" } },
                            title: { text: null },
                            time: {
                                getTimezoneOffset: function (e) {
                                    var t = document.querySelector("meta[name=time-zone]").content;
                                    return -spacetime(e, t).offset();
                                },
                            },
                            xAxis: { lineWidth: 0, tickWidth: 0, labels: { style: { color: a.a.gray[600] } }, title: { style: { color: a.a.gray[600] } } },
                            yAxis: {
                                softMin: 0,
                                softMax: 1,
                                title: { align: "high", style: { color: a.a.gray[600] } },
                                labels: { overflow: "justify", style: { color: a.a.gray[600] } },
                                gridLineColor: a.a.gray[900],
                                gridLineDashStyle: "ShortDot",
                            },
                            tooltip: { useHTML: !0, borderWidth: 0, borderRadius: 8, backgroundColor: "#efefef", padding: 15, style: { color: "#66636b" } },
                            legend: { itemMarginTop: 8, itemDistance: 30, itemStyle: { color: a.a.gray[600] }, itemHoverStyle: { color: a.a.gray[600] }, itemHiddenStyle: { color: a.a.gray[700] } },
                            credits: { enabled: !1 },
                            plotOptions: {
                                column: { color: a.a.primary[700], borderWidth: 0, borderRadius: 4 },
                                line: {
                                    lineWidth: 2,
                                    marker: { enabled: !1, symbol: "circle", lineColor: a.a.black, states: { hover: { enabled: !0, radius: 5, lineWidthPlus: 2 } } },
                                    events: {
                                        mouseOver: function () {
                                            this.group.toFront(), this.markerGroup.toFront();
                                        },
                                    },
                                },
                                series: { animation: { easing: r.a.easing("easeInCubic") }, states: { inactive: { opacity: 1 } } },
                            },
                        })
                    );
                },
            };
        },
        function (e, t, n) {
            "use strict";
            var r = n(56);
            t.a = function (e, t) {
                if (0 === t) return Object(r.t)("pagination.no_items");
                if (t < 10) return Object(r.t)("pagination.single_page", { count: t });
                var n = 10 * (e - 1),
                    a = n + 1,
                    i = n + 10;
                return Object(r.t)("pagination.multiple_pages", { from: a, to: i, count: t });
            };
        },
        ,
        ,
        function (e, t, n) {
            "use strict";
            n(128),
                n(143),
                n(144),
                n(145),
                n(146),
                n(147),
                n(148),
                n(149),
                n(150),
                n(151),
                n(152),
                n(153),
                n(154),
                n(155),
                n(156),
                n(159),
                n(164),
                n(165),
                n(166),
                n(167),
                n(168),
                n(169),
                n(170),
                n(171),
                n(172),
                n(174),
                n(175),
                n(176),
                n(177),
                n(178),
                n(179),
                n(181),
                n(182),
                n(115),
                n(186),
                n(187),
                n(188),
                n(189),
                n(190),
                n(191),
                n(192),
                n(193),
                n(194),
                n(195),
                n(196),
                n(197),
                n(198),
                n(199),
                n(201),
                n(202),
                n(203),
                n(204),
                n(205),
                n(206),
                n(207),
                n(208),
                n(209),
                n(210),
                n(211),
                n(212),
                n(213),
                n(214),
                n(215),
                n(216),
                n(217),
                n(218),
                n(219),
                n(220),
                n(221),
                n(222);
        },
        ,
        ,
        ,
        ,
        ,
        ,
        ,
        ,
        ,
        ,
        ,
        ,
        ,
        ,
        ,
        ,
        ,
        ,
        function (e, t, n) {
            "use strict";
            var r = n(13),
                a = n(99),
                i = n(86),
                o = new a.a({ key: "81ba898c-1e89-425f-96cc-98dbf5405ff1" });
            r.default.config.errorHandler = Object(i.a)(o, r.default);
        },
        ,
        ,
        ,
        function (e, t) {},
        function (e, t, n) {
            e.exports = n.p + "media/images/feather-sprite-cee9060cc7a367341de25b7b33f54c40.svg";
        },
        function (e, t, n) {
            e.exports = n.p + "media/images/plugins/icons-4a6517978d199f2f7010920e113649b1.svg";
        },
        ,
        ,
        ,
        ,
        ,
        ,
        ,
        ,
        ,
        ,
        ,
        ,
        ,
        ,
        ,
        ,
        ,
        ,
        ,
        ,
        ,
        ,
        ,
        ,
        ,
        ,
        ,
        ,
        ,
        ,
        ,
        ,
        ,
        ,
        ,
        ,
        ,
        ,
        ,
        ,
        ,
        ,
        ,
        ,
        ,
        ,
        ,
        ,
        ,
        ,
        ,
        ,
        ,
        ,
        ,
        ,
        ,
        ,
        ,
        ,
        ,
        ,
        ,
        ,
        ,
        ,
        ,
        ,
        ,
        ,
        ,
        ,
        ,
        ,
        ,
        ,
        ,
        ,
        ,
        ,
        ,
        ,
        ,
        ,
        ,
        ,
        ,
        ,
        ,
        ,
        ,
        ,
        ,
        ,
        ,
        ,
        ,
        ,
        ,
        ,
        ,
        ,
        ,
        ,
        ,
        ,
        ,
        ,
        function (e, t, n) {
            "use strict";
            (function (e) {
                (e._exposes = {}),
                    (t.a = function (t, n) {
                        e._exposes[t] = { $emit: n.$emit.bind(n) };
                    });
            }.call(this, n(28)));
        },
        ,
        ,
        ,
        ,
        ,
        ,
        ,
        ,
        ,
        function (e, t, n) {
            "use strict";
            (function (e) {
                var r = n(56),
                    a = n.n(r);
                (e.i18n = a.a),
                    (t.a = {
                        install: function (e, t) {
                            e.mixin({ methods: { t: a.a.t } });
                        },
                    });
            }.call(this, n(28)));
        },
        ,
        ,
        ,
        ,
        ,
        ,
        ,
        function (e, t, n) {
            n(405), (e.exports = n(402));
        },
        ,
        ,
        ,
        ,
        ,
        ,
        ,
        ,
        ,
        ,
        ,
        ,
        ,
        ,
        ,
        ,
        ,
        ,
        ,
        ,
        ,
        ,
        ,
        ,
        ,
        ,
        ,
        ,
        function (e, t, n) {
            var r = {
                "./mk-activities-page.js": 283,
                "./mk-activities-table.vue": 407,
                "./mk-appearance.js": 298,
                "./mk-builder.js": 299,
                "./mk-certificate-text.vue": 408,
                "./mk-certificate.vue": 409,
                "./mk-colorpicker.vue": 410,
                "./mk-comment-form.vue": 411,
                "./mk-comment.js": 307,
                "./mk-direct-uploads.vue": 412,
                "./mk-dropzone.vue": 413,
                "./mk-grid.js": 422,
                "./mk-label-toggle.vue": 414,
                "./mk-membership-level.vue": 415,
                "./mk-membership-levels.js": 309,
                "./mk-modal.vue": 416,
                "./mk-multiselect.vue": 417,
                "./mk-notification.js": 310,
                "./mk-player.js": 311,
                "./mk-rankings-page.js": 312,
                "./mk-rankings-table.vue": 418,
                "./mk-rating.vue": 419,
                "./mk-roles.vue": 420,
                "./mk-schedule.vue": 421,
                "./mk-schedules.js": 313,
                "./mk-section.js": 314,
                "./mk-sortable.js": 315,
                "./mk-tabs.js": 316,
            };
            function a(e) {
                var t = i(e);
                return n(t);
            }
            function i(e) {
                if (!n.o(r, e)) {
                    var t = new Error("Cannot find module '" + e + "'");
                    throw ((t.code = "MODULE_NOT_FOUND"), t);
                }
                return r[e];
            }
            (a.keys = function () {
                return Object.keys(r);
            }),
                (a.resolve = i),
                (e.exports = a),
                (a.id = 282);
        },
        function (e, t, n) {
            "use strict";
            n.r(t);
            var r = n(12),
                a = n.n(r),
                i = n(97),
                o = n(14),
                s = n(22),
                c = n(98);
            function l(e, t, n, r, a, i, o) {
                try {
                    var s = e[i](o),
                        c = s.value;
                } catch (l) {
                    return void n(l);
                }
                s.done ? t(c) : Promise.resolve(c).then(r, a);
            }
            function u(e) {
                return function () {
                    var t = this,
                        n = arguments;
                    return new Promise(function (r, a) {
                        var i = e.apply(t, n);
                        function o(e) {
                            l(i, r, a, o, s, "next", e);
                        }
                        function s(e) {
                            l(i, r, a, o, s, "throw", e);
                        }
                        o(void 0);
                    });
                };
            }
            function d(e, t) {
                return (
                    (function (e) {
                        if (Array.isArray(e)) return e;
                    })(e) ||
                    (function (e, t) {
                        if ("undefined" === typeof Symbol || !(Symbol.iterator in Object(e))) return;
                        var n = [],
                            r = !0,
                            a = !1,
                            i = void 0;
                        try {
                            for (var o, s = e[Symbol.iterator](); !(r = (o = s.next()).done) && (n.push(o.value), !t || n.length !== t); r = !0);
                        } catch (c) {
                            (a = !0), (i = c);
                        } finally {
                            try {
                                r || null == s.return || s.return();
                            } finally {
                                if (a) throw i;
                            }
                        }
                        return n;
                    })(e, t) ||
                    (function (e, t) {
                        if (!e) return;
                        if ("string" === typeof e) return f(e, t);
                        var n = Object.prototype.toString.call(e).slice(8, -1);
                        "Object" === n && e.constructor && (n = e.constructor.name);
                        if ("Map" === n || "Set" === n) return Array.from(e);
                        if ("Arguments" === n || /^(?:Ui|I)nt(?:8|16|32)(?:Clamped)?Array$/.test(n)) return f(e, t);
                    })(e, t) ||
                    (function () {
                        throw new TypeError("Invalid attempt to destructure non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method.");
                    })()
                );
            }
            function f(e, t) {
                (null == t || t > e.length) && (t = e.length);
                for (var n = 0, r = new Array(t); n < t; n++) r[n] = e[n];
                return r;
            }
            t.default = {
                props: { series: { type: Object, required: !0 }, records: { type: Array, required: !0 }, recordsCount: { type: Number, required: !0 } },
                data: function () {
                    return { loading: !1, localSeries: this.series, localRecords: this.records, localRecordsCount: this.recordsCount, group: "course_id", range: "last_thirty_days", sortBy: "", sort: "", page: 1, uniqueSeries: {} };
                },
                computed: {
                    tooltipFormat: function () {
                        return ["today", "yesterday"].includes(this.range) ? "%A, %kh" : "%A, %e %b %Y";
                    },
                    typeTitle: function () {
                        return this.t("activities_page.headers.".concat(this.group));
                    },
                    paginationMessage: function () {
                        return Object(c.a)(this.page, this.localRecordsCount);
                    },
                },
                watch: {
                    group: function () {
                        this.fetch();
                    },
                    page: function () {
                        this.fetch();
                    },
                    range: function () {
                        this.fetch(!0);
                    },
                    localSeries: function (e) {
                        this.generateUniqueSeries(), this.setData("LessonStatus"), this.setData("Rating"), this.setData("Comment"), this.chart.redraw();
                    },
                },
                mounted: function () {
                    i.a.start(), this.generateUniqueSeries(), (this.chart = this.renderChart());
                },
                methods: {
                    format: function (e) {
                        return Highcharts.numberFormat(e, 0, ",", ".");
                    },
                    generateUniqueSeries: function () {
                        var e = this;
                        Object.entries(this.localSeries).forEach(function (t) {
                            var n = d(t, 2),
                                r = n[0],
                                a = n[1];
                            (e.uniqueSeries[r] = {}),
                                a.forEach(function (t) {
                                    e.uniqueSeries[r][Date.parse(t[0])] = t[2];
                                });
                        });
                    },
                    groupBy: function (e) {
                        (this.group = e), (this.page = 1);
                    },
                    filterBy: function (e) {
                        var t = this.localSeries[e];
                        if (t)
                            return t.map(function (e) {
                                return [Date.parse(e[0]), e[1]];
                            });
                    },
                    setData: function (e) {
                        this.chart.get(e).setData(this.filterBy(e), !1, !0, !1);
                    },
                    fetch: function () {
                        var e = arguments,
                            t = this;
                        return u(
                            a.a.mark(function n() {
                                var r, i;
                                return a.a.wrap(function (n) {
                                    for (;;)
                                        switch ((n.prev = n.next)) {
                                            case 0:
                                                return (
                                                    (r = e.length > 0 && void 0 !== e[0] && e[0]),
                                                    (t.loading = !0),
                                                    (n.next = 4),
                                                    s.a.get("/reports/activities.json", { searchParams: { sort_by: t.sortBy, sort: t.sort, group: t.group, range: t.range, page: t.page, series: r } }).json()
                                                );
                                            case 4:
                                                (i = n.sent), (t.loading = !1), (t.localRecords = i.records), (t.localRecordsCount = i.total_records), r && (t.localSeries = i.series);
                                            case 9:
                                            case "end":
                                                return n.stop();
                                        }
                                }, n);
                            })
                        )();
                    },
                    handleSort: function (e) {
                        var t = e.column,
                            n = e.direction;
                        this.loading || ((this.sortBy = t), (this.sort = n), this.fetch());
                    },
                    renderChart: function () {
                        var e = this;
                        return new Highcharts.Chart({
                            chart: { renderTo: this.$refs.chart, spacingBottom: 0 },
                            xAxis: { type: "datetime", dateTimeLabelFormats: { day: "%e %b", week: "%e %b" } },
                            yAxis: { allowDecimals: !1, title: { text: this.t("activities_page.chart.y_axis_title") } },
                            tooltip: {
                                crosshairs: { color: o.a.gray[900], dashStyle: "solid" },
                                formatter: function () {
                                    var t = this.series.options.id,
                                        n = e.uniqueSeries[t][this.x];
                                    return '\n              <div style="color: '
                                        .concat(this.color, '" class="text-gray font-weight-bold">')
                                        .concat(this.series.name, '</div>\n              <div class="text-body">')
                                        .concat(Highcharts.dateFormat(e.tooltipFormat, new Date(this.x)), '</div>\n              <h1 class="text-gray mb-0 mt-1">\n                <span class="align-middle">')
                                        .concat(e.format(this.y), '</span>\n                <small class="text-sm align-middle badge badge-soft-secondary">\n                  ')
                                        .concat(e.t("activities_page.chart.members_count", { count: n, num: e.format(n) }), "\n                </small>\n              </h1>\n            ");
                                },
                            },
                            plotOptions: { series: { states: { hover: { halo: { size: 0 } } } } },
                            series: [
                                { id: "LessonStatus", name: this.t("activities_page.chart.lesson_statuses"), data: this.filterBy("LessonStatus"), color: "#2c7be5" },
                                { id: "Rating", name: this.t("activities_page.chart.ratings"), data: this.filterBy("Rating"), color: "#727cf5" },
                                { id: "Comment", name: this.t("activities_page.chart.comments"), data: this.filterBy("Comment"), color: "#02a8b5" },
                            ],
                        });
                    },
                },
            };
        },
        ,
        ,
        ,
        ,
        ,
        ,
        ,
        ,
        ,
        ,
        ,
        ,
        ,
        ,
        function (e, t, n) {
            "use strict";
            n.r(t);
            var r = n(36),
                a = n(14);
            function i(e) {
                return (
                    (function (e) {
                        if (Array.isArray(e)) return o(e);
                    })(e) ||
                    (function (e) {
                        if ("undefined" !== typeof Symbol && Symbol.iterator in Object(e)) return Array.from(e);
                    })(e) ||
                    (function (e, t) {
                        if (!e) return;
                        if ("string" === typeof e) return o(e, t);
                        var n = Object.prototype.toString.call(e).slice(8, -1);
                        "Object" === n && e.constructor && (n = e.constructor.name);
                        if ("Map" === n || "Set" === n) return Array.from(e);
                        if ("Arguments" === n || /^(?:Ui|I)nt(?:8|16|32)(?:Clamped)?Array$/.test(n)) return o(e, t);
                    })(e) ||
                    (function () {
                        throw new TypeError("Invalid attempt to spread non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method.");
                    })()
                );
            }
            function o(e, t) {
                (null == t || t > e.length) && (t = e.length);
                for (var n = 0, r = new Array(t); n < t; n++) r[n] = e[n];
                return r;
            }
            t.default = {
                data: function () {
                    return { navColorized: Object(r.a)("academy_setting_attributes_theme_nav_colorized"), navBgPrimary: "", navBgSecondary: "", brandColor: "" };
                },
                watch: {
                    navBgPrimary: function (e) {
                        a.a.set("nav-bg-primary", e.rgba, { alpha: 0.9 });
                    },
                    navBgSecondary: function (e) {
                        a.a.set("nav-bg-secondary", e.rgba, { alpha: 0.9 });
                    },
                    brandColor: function (e) {
                        a.a.set("brand", e.hex);
                    },
                    navColorized: function (e) {
                        var t = ["navbar-dark", "navbar-vibrant"],
                            n = ["navbar-light"];
                        return this.toggleNavClass.apply(this, i(e ? [t, n] : [n, t]));
                    },
                },
                mounted: function () {
                    document.addEventListener("click", this.blurColorpickers);
                },
                destroyed: function () {
                    a.a.unset("nav-bg-primary"), a.a.unset("nav-bg-secondary"), a.a.unset("brand"), document.removeEventListener("click", this.blurColorpickers);
                },
                methods: {
                    hideAllColorpickers: function () {
                        (this.$refs.primaryColor.open = !1), (this.$refs.secondaryColor.open = !1), (this.$refs.brandColor.open = !1);
                    },
                    blurColorpickers: function (e) {
                        e.target.closest("label") || this.hideAllColorpickers();
                    },
                    toggleNavClass: function (e, t) {
                        var n = document.querySelector(".navbar");
                        e.forEach(function (e) {
                            return n.classList.add(e);
                        }),
                            t.forEach(function (e) {
                                return n.classList.remove(e);
                            });
                    },
                },
            };
        },
        function (e, t, n) {
            "use strict";
            n.r(t);
            var r = n(12),
                a = n.n(r),
                i = n(36),
                o = n(22);
            function s(e) {
                return (
                    (function (e) {
                        if (Array.isArray(e)) return c(e);
                    })(e) ||
                    (function (e) {
                        if ("undefined" !== typeof Symbol && Symbol.iterator in Object(e)) return Array.from(e);
                    })(e) ||
                    (function (e, t) {
                        if (!e) return;
                        if ("string" === typeof e) return c(e, t);
                        var n = Object.prototype.toString.call(e).slice(8, -1);
                        "Object" === n && e.constructor && (n = e.constructor.name);
                        if ("Map" === n || "Set" === n) return Array.from(e);
                        if ("Arguments" === n || /^(?:Ui|I)nt(?:8|16|32)(?:Clamped)?Array$/.test(n)) return c(e, t);
                    })(e) ||
                    (function () {
                        throw new TypeError("Invalid attempt to spread non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method.");
                    })()
                );
            }
            function c(e, t) {
                (null == t || t > e.length) && (t = e.length);
                for (var n = 0, r = new Array(t); n < t; n++) r[n] = e[n];
                return r;
            }
            function l(e, t, n, r, a, i, o) {
                try {
                    var s = e[i](o),
                        c = s.value;
                } catch (l) {
                    return void n(l);
                }
                s.done ? t(c) : Promise.resolve(c).then(r, a);
            }
            t.default = {
                props: { videos: { type: Array, required: !0 } },
                data: function () {
                    return { tab: "text", title: Object(i.a)("lesson_title"), videoId: Object(i.a)("lesson_video_id"), entries: this.videos, lastVideoId: null, loading: !1, search: "", destroyedFiles: [], selectedFiles: 0, uploading: !1 };
                },
                computed: {
                    filteredVideos: function () {
                        var e = this;
                        return this.entries
                            .filter(function (t) {
                                return t.title.toLowerCase().indexOf(e.search.toLowerCase()) > -1;
                            })
                            .slice(0, 1e3);
                    },
                    uploadButtonText: function () {
                        return this.selectedFiles >= 1 ? this.t("builder.file_count", { count: this.selectedFiles }) : "Upload";
                    },
                    selectedFilesLabel: function () {
                        return this.t("builder.selected", { count: this.selectedFiles });
                    },
                },
                watch: {
                    tab: function () {
                        "video" === this.tab && (this.entries.length || this.fetchVideos(), this.$nextTick(this.scrollToSelected));
                    },
                },
                mounted: function () {
                    this.$refs.file.addEventListener("change", this.handleFileSelected);
                },
                methods: {
                    handleDrop: function (e) {
                        e.preventDefault(), (this.$refs.file.files = e.dataTransfer.files), (this.selectedFiles = e.dataTransfer.files.length);
                    },
                    handleFileSelected: function (e) {
                        this.selectedFiles = e.target.files.length;
                    },
                    scrollToSelected: function () {
                        var e = this.$el.querySelector('div[data-id="'.concat(this.videoId, '"]'));
                        e && (e.closest(".card-body").scrollTop = e.offsetTop - e.offsetHeight / 2);
                    },
                    isTitleChangeable: function () {
                        var e = this;
                        return (
                            this.entries.filter(function (t) {
                                return t.title === e.title;
                            }).length || "" === this.title
                        );
                    },
                    stopPolling: function () {
                        clearInterval(this.polling), (this.loading = !1);
                    },
                    startPolling: function () {
                        var e,
                            t = this;
                        return ((e = a.a.mark(function e() {
                            var n, r, i;
                            return a.a.wrap(function (e) {
                                for (;;)
                                    switch ((e.prev = e.next)) {
                                        case 0:
                                            return (
                                                (t.lastVideoId = t.lastVideoId
                                                    ? t.lastVideoId
                                                    : Math.max.apply(
                                                          Math,
                                                          s(
                                                              t.entries.map(function (e) {
                                                                  return e.id;
                                                              })
                                                          ).concat([0])
                                                      )),
                                                (r = { since: t.lastVideoId }),
                                                (e.next = 4),
                                                o.a.get("/videos", { searchParams: r }).json()
                                            );
                                        case 4:
                                            if ("" !== (i = e.sent)) {
                                                e.next = 7;
                                                break;
                                            }
                                            return e.abrupt("return", t.stopPolling());
                                        case 7:
                                            (t.lastVideoId = i.length ? i[0].id : t.lastVideoId), (n = t.entries).unshift.apply(n, s(i));
                                        case 9:
                                        case "end":
                                            return e.stop();
                                    }
                            }, e);
                        })),
                        function () {
                            var t = this,
                                n = arguments;
                            return new Promise(function (r, a) {
                                var i = e.apply(t, n);
                                function o(e) {
                                    l(i, r, a, o, s, "next", e);
                                }
                                function s(e) {
                                    l(i, r, a, o, s, "throw", e);
                                }
                                o(void 0);
                            });
                        })();
                    },
                    fetchVideos: function () {
                        (this.loading = !0), (this.polling = setInterval(this.startPolling, 2e3)), o.a.post("/videos");
                    },
                    selectedClass: function (e) {
                        return { "card-selected": this.videoId == e.id };
                    },
                    unselect: function () {
                        (this.videoId = ""), this.isTitleChangeable() && (this.title = "");
                    },
                    select: function (e) {
                        if (this.videoId == e.id) return this.unselect();
                        this.isTitleChangeable() && (this.title = e.title), (this.videoId = e.id);
                    },
                },
            };
        },
        ,
        ,
        ,
        ,
        ,
        ,
        ,
        function (e, t, n) {
            "use strict";
            n.r(t),
                (t.default = {
                    data: function () {
                        return { visible: !0, editing: !1, replying: !1, nestedComments: [] };
                    },
                    watch: {
                        editing: function (e) {
                            (this.replying = !1), this.$refs.form.classList.toggle("d-none", !e), this.$refs.inner.classList.toggle("d-none", e), this.$refs.content.focus();
                        },
                    },
                    methods: {
                        prependComment: function (e) {
                            this.nestedComments.unshift({ template: e }), (this.replying = !1);
                        },
                        updated: function () {
                            (this.$refs.inner.textContent = this.$refs.content.value), (this.editing = !1);
                        },
                        reply: function () {
                            var e = this;
                            (this.replying = !0),
                                this.$nextTick(function (t) {
                                    e.$el.querySelector(".new_comment textarea").focus();
                                });
                        },
                        deleted: function () {
                            this.visible = !1;
                        },
                    },
                });
        },
        ,
        function (e, t, n) {
            "use strict";
            n.r(t), (t.default = { props: { records: { type: Array, required: !0 } } });
        },
        function (e, t, n) {
            "use strict";
            n.r(t),
                (t.default = {
                    props: { fx: { type: String, default: "fadeOutUp" } },
                    data: function () {
                        return { visible: !0 };
                    },
                    mounted: function () {
                        var e = this;
                        this.interval = setTimeout(function () {
                            e.close();
                        }, 5e3);
                    },
                    methods: {
                        close: function () {
                            var e = this;
                            clearInterval(this.interval),
                                this.$el.classList.add(this.fx),
                                setTimeout(function () {
                                    e.visible = !1;
                                }, 505);
                        },
                    },
                });
        },
        function (e, t, n) {
            "use strict";
            n.r(t);
            var r = n(239),
                a = n(240),
                i = n.n(a),
                o = n(14);
            t.default = {
                props: {
                    type: {
                        type: String,
                        required: !0,
                        validator: function (e) {
                            return -1 !== ["youtube", "vimeo"].indexOf(e);
                        },
                    },
                    uid: { type: String, required: !0 },
                    time: { type: Number, required: !0 },
                    trackUrl: { type: String, required: !0 },
                },
                mounted: function () {
                    (this.playerEl = this.$refs.player),
                        "youtube" === this.type ? (this.initializeYouTubeAPI(), this.initializeYouTubePlayer()) : "vimeo" === this.type && this.initializeVimeo(),
                        this.hasBeacon() && (window.addEventListener("unload", this.track), window.addEventListener("turbolinks:before-visit", this.track));
                },
                beforeDestroy: function () {
                    this.hasBeacon() && (window.removeEventListener("unload", this.track), window.removeEventListener("turbolinks:before-visit", this.track));
                },
                methods: {
                    hasBeacon: function () {
                        return "sendBeacon" in navigator;
                    },
                    hasYouTube: function () {
                        return window.YT && "function" === typeof window.YT.Player;
                    },
                    initializeYouTubeAPI: function () {
                        this.hasYouTube() || ((window.onYouTubePlayerAPIReady = this.initializeYouTubePlayer), i()("https://www.youtube.com/player_api"));
                    },
                    initializeYouTubePlayer: function () {
                        var e = this;
                        if (this.hasYouTube())
                            return (
                                (this.wrapper = document.createElement("div")),
                                this.playerEl.appendChild(this.wrapper),
                                new YT.Player(this.wrapper, {
                                    width: 640,
                                    height: 360,
                                    videoId: this.uid,
                                    origin: window.location.href,
                                    playerVars: { modestbranding: !0, iv_load_policy: 3, cc_load_policy: 0, showinfo: 0, rel: 0, start: this.time },
                                    events: {
                                        onReady: function (t) {
                                            e.player = t.target;
                                        },
                                    },
                                })
                            );
                    },
                    initializeVimeo: function () {
                        var e = this;
                        (this.started = !1),
                            (this.player = new r.a(this.playerEl, { width: 640, id: this.uid, title: !1, byline: !1, portrait: !1, sidedock: !1, transparent: !1, color: o.a.get("brand", "#6700ff").replace("#", "") })),
                            this.player.on("play", function () {
                                e.started || (e.player.setCurrentTime(e.time), (e.started = !0));
                            }),
                            this.player.on("seeked", function () {
                                e.started = !0;
                            }),
                            this.player.on("timeupdate", function (t) {
                                e.currentTime = t.seconds;
                            });
                    },
                    trackable: function () {
                        return this.currentTime > 0 && this.currentTime !== this.time;
                    },
                    persist: function () {
                        var e = this;
                        this.tracking = !0;
                        var t = new FormData();
                        t.append("time", this.currentTime),
                            navigator.sendBeacon(this.trackUrl, t),
                            setTimeout(function () {
                                e.tracking = !1;
                            }, 1e3);
                    },
                    track: function () {
                        if (!this.tracking) return this.player && "youtube" === this.type && (this.currentTime = this.player.getCurrentTime()), this.trackable() ? this.persist() : null;
                    },
                },
            };
        },
        function (e, t, n) {
            "use strict";
            n.r(t);
            var r = n(12),
                a = n.n(r),
                i = n(97),
                o = n(22),
                s = n(98);
            function c(e, t, n, r, a, i, o) {
                try {
                    var s = e[i](o),
                        c = s.value;
                } catch (l) {
                    return void n(l);
                }
                s.done ? t(c) : Promise.resolve(c).then(r, a);
            }
            function l(e) {
                return function () {
                    var t = this,
                        n = arguments;
                    return new Promise(function (r, a) {
                        var i = e.apply(t, n);
                        function o(e) {
                            c(i, r, a, o, s, "next", e);
                        }
                        function s(e) {
                            c(i, r, a, o, s, "throw", e);
                        }
                        o(void 0);
                    });
                };
            }
            t.default = {
                props: { series: { type: Array, required: !0 }, records: { type: Array, required: !0 }, recordsCount: { type: Number, required: !0 } },
                data: function () {
                    return { loading: !1, localSeries: this.series, localRecords: this.records, localRecordsCount: this.recordsCount, classroom: "", sortBy: "", sort: "", range: "", page: 1 };
                },
                computed: {
                    classrooms: function () {
                        return this.series.map(function (e) {
                            return { id: e.classroom_id, name: "".concat(e.course_name, " (").concat(e.classroom_name, ")") };
                        });
                    },
                    paginationMessage: function () {
                        return Object(s.a)(this.page, this.localRecordsCount);
                    },
                },
                watch: {
                    classroom: function () {
                        this.fetch();
                    },
                    page: function () {
                        this.fetch();
                    },
                    range: function () {
                        this.fetch(!0);
                    },
                    localSeries: function (e) {
                        var t = this,
                            n = e.map(function (e) {
                                return e.classroom_id;
                            });
                        e.forEach(function (e) {
                            return t.setData(e);
                        }),
                            this.chart.series
                                .filter(function (e) {
                                    return !n.includes(e.options.id);
                                })
                                .forEach(function (e) {
                                    return e.remove(!1);
                                }),
                            this.chart.redraw();
                    },
                },
                mounted: function () {
                    i.a.start(), (this.chart = this.renderChart()), this.classrooms.length && (this.classroom = this.classrooms[0].id);
                },
                methods: {
                    setData: function (e) {
                        var t = this.chart.get(e.classroom_id);
                        t
                            ? t.points[0].update({ x: e.average_progress, y: e.rate, color: this.colorize(e) }, !1)
                            : this.chart.addSeries({ id: e.classroom_id, name: "".concat(e.course_name, " (").concat(e.classroom_name, ")"), data: [[e.average_progress, e.rate]], color: this.colorize(e) }, !1);
                    },
                    handleSort: function (e) {
                        var t = e.column,
                            n = e.direction;
                        this.loading || ((this.sortBy = t), (this.sort = n), this.fetch());
                    },
                    fetch: function () {
                        var e = arguments,
                            t = this;
                        return l(
                            a.a.mark(function n() {
                                var r, i;
                                return a.a.wrap(function (n) {
                                    for (;;)
                                        switch ((n.prev = n.next)) {
                                            case 0:
                                                return (
                                                    (r = e.length > 0 && void 0 !== e[0] && e[0]),
                                                    (t.loading = !0),
                                                    (n.next = 4),
                                                    o.a.get("/reports/rankings.json", { searchParams: { sort_by: t.sortBy, sort: t.sort, classroom: t.classroom, page: t.page, range: t.range, series: r } }).json()
                                                );
                                            case 4:
                                                (i = n.sent), (t.loading = !1), (t.localRecords = i.records), (t.localRecordsCount = i.total_records), r && (t.localSeries = i.series);
                                            case 9:
                                            case "end":
                                                return n.stop();
                                        }
                                }, n);
                            })
                        )();
                    },
                    colorize: function (e) {
                        var t = [Math.round(e.average_progress), Math.round(e.rate)],
                            n = t[0],
                            r = t[1];
                        switch (!0) {
                            case r >= 60 && n <= 60:
                                return "#2c7be5";
                            case r >= 40 && n <= 60:
                                return "#ffffff";
                            case n <= 30:
                                return "#ff679b";
                            case n >= 31 && n <= 60:
                                return "#727cf5";
                            case n >= 61 && n <= 70:
                                return "#2c7be5";
                            case n >= 71:
                                return "#00d97e";
                            default:
                                return "#ffffff";
                        }
                    },
                    generateSeries: function () {
                        var e = this;
                        return this.localSeries.map(function (t) {
                            return { id: t.classroom_id, name: "".concat(t.course_name, " (").concat(t.classroom_name, ")"), data: [[t.average_progress, t.rate]], color: e.colorize(t) };
                        });
                    },
                    renderChart: function () {
                        var e = this.t;
                        return new Highcharts.Chart({
                            chart: { renderTo: this.$refs.chart, type: "scatter", zoomType: "x", spacingBottom: 6 },
                            legend: { enabled: !1 },
                            xAxis: { min: 0, max: 100, title: { text: e("rankings_page.x_axis_title") }, labels: { format: "{value}%" } },
                            yAxis: { min: 0, max: 100, title: { text: e("rankings_page.y_axis_title") }, labels: { format: "{value}%" } },
                            plotOptions: { scatter: { marker: { radius: 5, symbol: "circle", states: { hover: { lineWidthPlus: 0 } } } } },
                            tooltip: {
                                formatter: function () {
                                    return '\n              <div class="text-body font-weight-bold">'
                                        .concat(this.series.name, '</div>\n              <div class="text-gray">\n                <strong>')
                                        .concat(this.x.toFixed(0), "%</strong> ")
                                        .concat(e("rankings_page.x_axis_title"), '\n              </div>\n              <div class="text-gray">\n                <strong>')
                                        .concat(this.y.toFixed(0), "%</strong> ")
                                        .concat(e("rankings_page.y_axis_title"), "\n              </div>\n            ");
                                },
                            },
                            series: this.generateSeries(),
                        });
                    },
                },
            };
        },
        function (e, t, n) {
            "use strict";
            function r(e, t) {
                return (
                    (function (e) {
                        if (Array.isArray(e)) return e;
                    })(e) ||
                    (function (e, t) {
                        if ("undefined" === typeof Symbol || !(Symbol.iterator in Object(e))) return;
                        var n = [],
                            r = !0,
                            a = !1,
                            i = void 0;
                        try {
                            for (var o, s = e[Symbol.iterator](); !(r = (o = s.next()).done) && (n.push(o.value), !t || n.length !== t); r = !0);
                        } catch (c) {
                            (a = !0), (i = c);
                        } finally {
                            try {
                                r || null == s.return || s.return();
                            } finally {
                                if (a) throw i;
                            }
                        }
                        return n;
                    })(e, t) ||
                    (function (e, t) {
                        if (!e) return;
                        if ("string" === typeof e) return a(e, t);
                        var n = Object.prototype.toString.call(e).slice(8, -1);
                        "Object" === n && e.constructor && (n = e.constructor.name);
                        if ("Map" === n || "Set" === n) return Array.from(e);
                        if ("Arguments" === n || /^(?:Ui|I)nt(?:8|16|32)(?:Clamped)?Array$/.test(n)) return a(e, t);
                    })(e, t) ||
                    (function () {
                        throw new TypeError("Invalid attempt to destructure non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method.");
                    })()
                );
            }
            function a(e, t) {
                (null == t || t > e.length) && (t = e.length);
                for (var n = 0, r = new Array(t); n < t; n++) r[n] = e[n];
                return r;
            }
            n.r(t),
                (t.default = {
                    props: { records: { type: Array, required: !0 }, courses: { type: Array, required: !0 }, lessons: { type: Object, required: !0 }, classroomId: { type: Number, required: !0 } },
                    data: function () {
                        return { scheduledSections: this.filterByType("Section"), scheduledLessons: this.filterByType("Lesson"), selectedLesson: null };
                    },
                    computed: {
                        groupedLessons: function () {
                            var e = this.scheduledLessons.map(function (e) {
                                return e.schedulable_id;
                            });
                            return Object.entries(this.lessons)
                                .map(function (t) {
                                    return [
                                        t[0],
                                        t[1].filter(function (t) {
                                            return !e.includes(t[1]);
                                        }),
                                    ];
                                })
                                .reduce(function (e, t) {
                                    return (e[t[0]] = t[1]), e;
                                }, {});
                        },
                    },
                    methods: {
                        filterByType: function (e) {
                            return this.records.filter(function (t) {
                                return t.schedulable_type === e;
                            });
                        },
                        findNameFor: function (e) {
                            for (var t = 0, n = Object.entries(this.lessons); t < n.length; t++) {
                                var a = r(n[t], 2),
                                    i = a[0],
                                    o = a[1].find(function (t) {
                                        return t[1] === e;
                                    });
                                if (o) return "".concat(i, " | ").concat(o[0]);
                            }
                        },
                        addSelected: function () {
                            this.selectedLesson
                                ? (this.scheduledLessons.push({ schedulable_id: this.selectedLesson, schedulable_type: "Lesson", classroom_id: this.classroomId, concatenated_name: this.findNameFor(this.selectedLesson) }),
                                  (this.selectedLesson = null))
                                : this.$refs.lessons.focus();
                        },
                    },
                });
        },
        function (e, t, n) {
            "use strict";
            n.r(t),
                (t.default = {
                    data: function () {
                        return { visible: !0 };
                    },
                });
        },
        function (e, t, n) {
            "use strict";
            n.r(t);
            var r = n(244),
                a = n(22);
            t.default = {
                props: { url: { type: String, required: !0 } },
                mounted: function () {
                    this.sortable = new r.a(this.$el, {
                        handle: "button[data-handle]",
                        draggable: "li",
                        animation: 150,
                        forceFallback: !0,
                        direction: "vertical",
                        easing: "cubic-bezier(1, 0, 0, 1)",
                        sort: !0,
                        onUpdate: this.handleUpdate,
                        onChoose: this.handleChoose,
                        onUnchoose: this.handleUnchoose,
                    });
                },
                methods: {
                    handleUpdate: function () {
                        a.a.patch(this.url, { json: { items: this.sortable.toArray() } });
                    },
                    handleChoose: function () {
                        this.$el.classList.add("sortable-active");
                    },
                    handleUnchoose: function () {
                        this.$el.classList.remove("sortable-active");
                    },
                },
            };
        },
        function (e, t, n) {
            "use strict";
            n.r(t),
                (t.default = {
                    props: { init: { type: String, required: !0 } },
                    data: function () {
                        return { tab: this.init };
                    },
                });
        },
        function (e, t, n) {
            var r = {
                "./astronaut.svg": 318,
                "./avatar.png": 319,
                "./countdown.png": 320,
                "./credit_card.svg": 321,
                "./customers/cachorros_incriveis.png": 322,
                "./customers/clube_do_valor.png": 323,
                "./customers/dale_carnegie.png": 324,
                "./customers/espaconave.png": 325,
                "./customers/kapsula.png": 326,
                "./customers/luiz.jpg": 327,
                "./customers/mauricio.jpg": 328,
                "./customers/rafael.jpg": 329,
                "./customers/scalper.png": 330,
                "./customers/spengler.jpg": 331,
                "./default_background.jpg": 332,
                "./devices/iphonex.svg": 333,
                "./devices/macbook.svg": 334,
                "./done.svg": 335,
                "./feather-sprite.svg": 125,
                "./graduation.svg": 336,
                "./illustration-3.png": 337,
                "./illustration-8.png": 338,
                "./integrations/activecampaign.png": 339,
                "./integrations/appmax.png": 340,
                "./integrations/braip.png": 341,
                "./integrations/eduzz.png": 342,
                "./integrations/hotmart.png": 343,
                "./integrations/keap.png": 344,
                "./integrations/mercadopago.png": 345,
                "./integrations/monetizze.png": 346,
                "./integrations/pagseguro.png": 347,
                "./integrations/rdstation.png": 348,
                "./integrations/vimeo.png": 349,
                "./integrations/youtube.png": 350,
                "./list-item.svg": 351,
                "./lock.svg": 352,
                "./logo.svg": 353,
                "./logos/active_campaign-icon.png": 354,
                "./logos/active_campaign.png": 355,
                "./logos/appmax-icon.png": 356,
                "./logos/appmax.png": 357,
                "./logos/braip-icon.png": 358,
                "./logos/braip.png": 359,
                "./logos/eduzz-icon.png": 360,
                "./logos/eduzz.png": 361,
                "./logos/evipes-icon.png": 362,
                "./logos/evipes.png": 363,
                "./logos/gist-icon.png": 364,
                "./logos/gist.png": 365,
                "./logos/hook-icon.png": 366,
                "./logos/hook.png": 367,
                "./logos/hotmart-icon.png": 368,
                "./logos/hotmart.png": 369,
                "./logos/hubsale-icon.png": 370,
                "./logos/hubsale.png": 371,
                "./logos/infusionsoft-icon.png": 372,
                "./logos/infusionsoft.png": 373,
                "./logos/mailgun-icon.png": 374,
                "./logos/mailgun.png": 375,
                "./logos/mercadopago-icon.png": 376,
                "./logos/mercadopago.png": 377,
                "./logos/monetizze-icon.png": 378,
                "./logos/monetizze.png": 379,
                "./logos/pagseguro-icon.png": 380,
                "./logos/pagseguro.png": 381,
                "./logos/rdstation-icon.png": 382,
                "./logos/rdstation.png": 383,
                "./logos/vimeo-icon.png": 384,
                "./logos/vimeo.png": 385,
                "./logos/yampi-icon.png": 386,
                "./logos/yampi.png": 387,
                "./logos/youtube-icon.png": 388,
                "./logos/youtube.png": 389,
                "./marker.svg": 390,
                "./play.svg": 391,
                "./plugins/icons.svg": 126,
                "./posts.svg": 392,
                "./process.svg": 393,
                "./screenshots/dobra.jpg": 394,
                "./screenshots/dobra_mobile.jpg": 395,
                "./screenshots/michelle.jpg": 396,
                "./screenshots/ranking.png": 397,
                "./screenshots/vidana.jpg": 398,
                "./security.svg": 399,
                "./text.svg": 400,
                "./time_management.svg": 401,
            };
            function a(e) {
                var t = i(e);
                return n(t);
            }
            function i(e) {
                if (!n.o(r, e)) {
                    var t = new Error("Cannot find module '" + e + "'");
                    throw ((t.code = "MODULE_NOT_FOUND"), t);
                }
                return r[e];
            }
            (a.keys = function () {
                return Object.keys(r);
            }),
                (a.resolve = i),
                (e.exports = a),
                (a.id = 317);
        },
        function (e, t, n) {
            e.exports = n.p + "media/images/astronaut-3fe7e9b1c9472251daa3a43f1ffc894d.svg";
        },
        function (e, t, n) {
            e.exports = n.p + "media/images/avatar-eef650aed8ca06b66e5afb77839b1f0a.png";
        },
        function (e, t, n) {
            e.exports = n.p + "media/images/countdown-1c42bff406d5286a05b52ce2e00067ec.png";
        },
        function (e, t, n) {
            e.exports = n.p + "media/images/credit_card-613cab7788a4c1faf338581b32acc2b4.svg";
        },
        function (e, t, n) {
            e.exports = n.p + "media/images/customers/cachorros_incriveis-9dfa906d72eb3d96f98cde4235bee808.png";
        },
        function (e, t, n) {
            e.exports = n.p + "media/images/customers/clube_do_valor-656a92ff52585f6cb4834863fcfad2de.png";
        },
        function (e, t, n) {
            e.exports = n.p + "media/images/customers/dale_carnegie-d422df22cef98e1cb02e5df5be4c881e.png";
        },
        function (e, t, n) {
            e.exports = n.p + "media/images/customers/espaconave-54fea144eb4cdf6298b6ccda6f3d7fb9.png";
        },
        function (e, t, n) {
            e.exports = n.p + "media/images/customers/kapsula-71359b574bfdea333572ac2e307198ca.png";
        },
        function (e, t, n) {
            e.exports = n.p + "media/images/customers/luiz-95944addd9f18db70142ae01ce5ad2f8.jpg";
        },
        function (e, t, n) {
            e.exports = n.p + "media/images/customers/mauricio-21093c93d4767630c4a114a01234dca8.jpg";
        },
        function (e, t, n) {
            e.exports = n.p + "media/images/customers/rafael-3e1dd93bfcce6a9cf68b0f8757ede798.jpg";
        },
        function (e, t, n) {
            e.exports = n.p + "media/images/customers/scalper-ac5be818037313bfe8ee595efebcec6e.png";
        },
        function (e, t, n) {
            e.exports = n.p + "media/images/customers/spengler-baa16f9a38e5c61abf8b62a6564a261c.jpg";
        },
        function (e, t, n) {
            e.exports = n.p + "media/images/default_background-5a57ed2e8f293cdbe930493e396b7f94.jpg";
        },
        function (e, t, n) {
            e.exports = n.p + "media/images/devices/iphonex-e0359d6e9e029a949d3c6f46d70db4bc.svg";
        },
        function (e, t, n) {
            e.exports = n.p + "media/images/devices/macbook-1805efdca9faf25d3a0d7c369b7fea91.svg";
        },
        function (e, t, n) {
            e.exports = n.p + "media/images/done-7c0632643eda585e2727a781541acdda.svg";
        },
        function (e, t, n) {
            e.exports = n.p + "media/images/graduation-1f69f746d6d5e65e2e72717363da8388.svg";
        },
        function (e, t, n) {
            e.exports = n.p + "media/images/illustration-3-36e7bbe2b543c98cb15e7ff551bbad96.png";
        },
        function (e, t, n) {
            e.exports = n.p + "media/images/illustration-8-770b8613c9ce25b171be3dbc7e8af262.png";
        },
        function (e, t, n) {
            e.exports = n.p + "media/images/integrations/activecampaign-b5ca467b9a7f64aa78a94ba5050c5e34.png";
        },
        function (e, t, n) {
            e.exports = n.p + "media/images/integrations/appmax-33e3584ff5b2cb48d7aaba10f3a4f605.png";
        },
        function (e, t, n) {
            e.exports = n.p + "media/images/integrations/braip-fcbeab5e56c327d8a8572c29c518e752.png";
        },
        function (e, t, n) {
            e.exports = n.p + "media/images/integrations/eduzz-4a89951e83fe7cab73458bc0944d13cd.png";
        },
        function (e, t, n) {
            e.exports = n.p + "media/images/integrations/hotmart-025cd1bbdf5d7e375d28021ffd1955ed.png";
        },
        function (e, t, n) {
            e.exports = n.p + "media/images/integrations/keap-924659165af41265c9dbff7e19916abb.png";
        },
        function (e, t, n) {
            e.exports = n.p + "media/images/integrations/mercadopago-0691f793ac03813e09fe02845b5f2217.png";
        },
        function (e, t, n) {
            e.exports = n.p + "media/images/integrations/monetizze-28c683e116e66de3a2ee3ecd3932f3bc.png";
        },
        function (e, t, n) {
            e.exports = n.p + "media/images/integrations/pagseguro-f374a2775daaefc21eaf182db3faa825.png";
        },
        function (e, t, n) {
            e.exports = n.p + "media/images/integrations/rdstation-8cea3eb2670d2fc4ebbc8175aa68650a.png";
        },
        function (e, t, n) {
            e.exports = n.p + "media/images/integrations/vimeo-bd20eaf0866059078360a40d2da2fc97.png";
        },
        function (e, t, n) {
            e.exports = n.p + "media/images/integrations/youtube-37033015f07c832ec4eab6ad815899ef.png";
        },
        function (e, t, n) {
            e.exports = n.p + "media/images/list-item-d90214c2dd72efc0ff65707a18bb441b.svg";
        },
        function (e, t, n) {
            e.exports = n.p + "media/images/lock-cc0c9e74f28bf83017afb62e192961c1.svg";
        },
        function (e, t, n) {
            e.exports = n.p + "media/images/logo-0a4a366fac86fe3ac1e586aba6cb0273.svg";
        },
        function (e, t, n) {
            e.exports = n.p + "media/images/logos/active_campaign-icon-2b1250a8a6b25bc75af927846f1b876b.png";
        },
        function (e, t, n) {
            e.exports = n.p + "media/images/logos/active_campaign-c1252c1ebc50175969184ce49e3aa510.png";
        },
        function (e, t, n) {
            e.exports = n.p + "media/images/logos/appmax-icon-eaa6ab0f73065727089f046dfaae91ff.png";
        },
        function (e, t, n) {
            e.exports = n.p + "media/images/logos/appmax-2b034edfe7e0848ff8b41d7b64cac457.png";
        },
        function (e, t, n) {
            e.exports = n.p + "media/images/logos/braip-icon-b4fb63a27ac227bf3069345aec2a7b43.png";
        },
        function (e, t, n) {
            e.exports = n.p + "media/images/logos/braip-7b5ac3afbf15439b44df6a1d2f77de30.png";
        },
        function (e, t, n) {
            e.exports = n.p + "media/images/logos/eduzz-icon-16d4bb5d59b9d489ca5348f69c0ea9b4.png";
        },
        function (e, t, n) {
            e.exports = n.p + "media/images/logos/eduzz-6331eaa4424a5c454ee1ec8b6eab5772.png";
        },
        function (e, t, n) {
            e.exports = n.p + "media/images/logos/evipes-icon-dbf114cd325d49e96c07c36d1b4eb96f.png";
        },
        function (e, t, n) {
            e.exports = n.p + "media/images/logos/evipes-72768e026dd0f0836e4659bd808eb350.png";
        },
        function (e, t, n) {
            e.exports = n.p + "media/images/logos/gist-icon-7949f7854f3d7596465a6d22d50b1b0d.png";
        },
        function (e, t, n) {
            e.exports = n.p + "media/images/logos/gist-77505ce0eb748a8ecc10cdbb6d0de27b.png";
        },
        function (e, t, n) {
            e.exports = n.p + "media/images/logos/hook-icon-50a62141044a2e2f2ce12c1b7a182c74.png";
        },
        function (e, t, n) {
            e.exports = n.p + "media/images/logos/hook-f02eae9a0c37b364448521346fa68d14.png";
        },
        function (e, t, n) {
            e.exports = n.p + "media/images/logos/hotmart-icon-0ecb8da01c376abf2bd7ca4ff12dff81.png";
        },
        function (e, t, n) {
            e.exports = n.p + "media/images/logos/hotmart-758244101192d77e60a53797ff5b4774.png";
        },
        function (e, t, n) {
            e.exports = n.p + "media/images/logos/hubsale-icon-0f26452c3d09b499b86f6557c770f5ac.png";
        },
        function (e, t, n) {
            e.exports = n.p + "media/images/logos/hubsale-64001c622f96f6434d8733d62b2298f0.png";
        },
        function (e, t, n) {
            e.exports = n.p + "media/images/logos/infusionsoft-icon-fece71af6e84824d28603810369bb407.png";
        },
        function (e, t, n) {
            e.exports = n.p + "media/images/logos/infusionsoft-5fed970ffaf5654c4a0957ec6acde193.png";
        },
        function (e, t, n) {
            e.exports = n.p + "media/images/logos/mailgun-icon-f8c4b282766e0dd56545c9668be2d903.png";
        },
        function (e, t, n) {
            e.exports = n.p + "media/images/logos/mailgun-aa54ef25e2164be4f7f664012ee3f7a7.png";
        },
        function (e, t, n) {
            e.exports = n.p + "media/images/logos/mercadopago-icon-241b756fd49505e6380ab3bac0a43825.png";
        },
        function (e, t, n) {
            e.exports = n.p + "media/images/logos/mercadopago-dc4ff6d478e4748bf2a6544df57cadc4.png";
        },
        function (e, t, n) {
            e.exports = n.p + "media/images/logos/monetizze-icon-2bcf5413e2a084d16f0ea129c6b12326.png";
        },
        function (e, t, n) {
            e.exports = n.p + "media/images/logos/monetizze-c182e806f2ee5ef8127b3887e22b840b.png";
        },
        function (e, t, n) {
            e.exports = n.p + "media/images/logos/pagseguro-icon-a8f0813025dd7d2065236c87c6b6badc.png";
        },
        function (e, t, n) {
            e.exports = n.p + "media/images/logos/pagseguro-068dae8cce6538971622e418e102d18a.png";
        },
        function (e, t, n) {
            e.exports = n.p + "media/images/logos/rdstation-icon-c1cd9af1938beeea74d89703a4093f6e.png";
        },
        function (e, t, n) {
            e.exports = n.p + "media/images/logos/rdstation-5aa9937b69773cf8aad7b704e9c23123.png";
        },
        function (e, t, n) {
            e.exports = n.p + "media/images/logos/vimeo-icon-783eb5e5043717922335a22dc696be1e.png";
        },
        function (e, t, n) {
            e.exports = n.p + "media/images/logos/vimeo-7ed9043cdb13422eaad2a20a94b4aada.png";
        },
        function (e, t, n) {
            e.exports = n.p + "media/images/logos/yampi-icon-eb4675738d4dd018cd61da35bbf4b536.png";
        },
        function (e, t, n) {
            e.exports = n.p + "media/images/logos/yampi-6480cafef41094ef7746b17daf4fb719.png";
        },
        function (e, t, n) {
            e.exports = n.p + "media/images/logos/youtube-icon-4423f147fe2923996c571d2cf4992593.png";
        },
        function (e, t, n) {
            e.exports = n.p + "media/images/logos/youtube-62ed5b10af423ea87dbd1055d5361fc4.png";
        },
        function (e, t, n) {
            e.exports = n.p + "media/images/marker-881e3a89e41d929b1041e2f1545149d7.svg";
        },
        function (e, t, n) {
            e.exports = n.p + "media/images/play-5e96a9b2216fe579cf4055c2553a77df.svg";
        },
        function (e, t, n) {
            e.exports = n.p + "media/images/posts-b01476b79e69a61c94ac12298c7ee8f5.svg";
        },
        function (e, t, n) {
            e.exports = n.p + "media/images/process-50940c81738127ba3469209e4a11a1b9.svg";
        },
        function (e, t, n) {
            e.exports = n.p + "media/images/screenshots/dobra-a4e7b4aedb6b8b75bd3c5ae30febacc5.jpg";
        },
        function (e, t, n) {
            e.exports = n.p + "media/images/screenshots/dobra_mobile-5bed39308f4168f64463155ff349f1a8.jpg";
        },
        function (e, t, n) {
            e.exports = n.p + "media/images/screenshots/michelle-f9ae31c60d37e4ffbdce0090e2d88eca.jpg";
        },
        function (e, t, n) {
            e.exports = n.p + "media/images/screenshots/ranking-b439f29119d653375933b729da2f4edb.png";
        },
        function (e, t, n) {
            e.exports = n.p + "media/images/screenshots/vidana-5f2768932ec72fd5558ff6a49e838310.jpg";
        },
        function (e, t, n) {
            e.exports = n.p + "media/images/security-8029d47fc59c094dd3ba2f6f080a3dc8.svg";
        },
        function (e, t, n) {
            e.exports = n.p + "media/images/text-8832446121df9f62700635192c6ef85e.svg";
        },
        function (e, t, n) {
            e.exports = n.p + "media/images/time_management-8e79f4603501fb4bc2adf458189b05d7.svg";
        },
        function (e, t, n) {},
        ,
        ,
        function (e, t, n) {
            "use strict";
            n.r(t);
            n(101), n(120);
            var r = n(229),
                a = n(34),
                i = n.n(a),
                o = n(13),
                s = n(87),
                c = n.n(s),
                l = n(230),
                u = n(231),
                d = n(249),
                f = n(252),
                m = n(251),
                p = n(250),
                h = n(233),
                g = n.n(h),
                v = n(42),
                b = {
                    mounted: function () {
                        (this.appended = !1), (this.overlay = document.createElement("div")), (this.overlay.className = "overlay d-none"), this.overlay.addEventListener("click", this.hideOverlay);
                    },
                    methods: {
                        hideOverlay: function () {
                            var e = this;
                            Object(v.a)({
                                targets: this.overlay,
                                opacity: 0,
                                duration: 250,
                                easing: "easeOutQuad",
                                complete: function () {
                                    e.overlay.classList.add("d-none"), document.body.classList.remove("overlay-open");
                                },
                            });
                        },
                        showOverlay: function () {
                            this.appended || document.body.appendChild(this.overlay),
                                (this.overlay.style.opacity = 0),
                                this.overlay.classList.remove("d-none"),
                                document.body.classList.add("overlay-open"),
                                Object(v.a)({ targets: this.overlay, opacity: 1, duration: 250, easing: "easeInQuad" });
                        },
                    },
                };
            function y(e) {
                return (
                    (function (e) {
                        if (Array.isArray(e)) return _(e);
                    })(e) ||
                    (function (e) {
                        if ("undefined" !== typeof Symbol && Symbol.iterator in Object(e)) return Array.from(e);
                    })(e) ||
                    (function (e, t) {
                        if (!e) return;
                        if ("string" === typeof e) return _(e, t);
                        var n = Object.prototype.toString.call(e).slice(8, -1);
                        "Object" === n && e.constructor && (n = e.constructor.name);
                        if ("Map" === n || "Set" === n) return Array.from(e);
                        if ("Arguments" === n || /^(?:Ui|I)nt(?:8|16|32)(?:Clamped)?Array$/.test(n)) return _(e, t);
                    })(e) ||
                    (function () {
                        throw new TypeError("Invalid attempt to spread non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method.");
                    })()
                );
            }
            function _(e, t) {
                (null == t || t > e.length) && (t = e.length);
                for (var n = 0, r = new Array(t); n < t; n++) r[n] = e[n];
                return r;
            }
            var x = {
                    mounted: function () {
                        document.addEventListener("click", this.hideDropdown), document.addEventListener("keyup", this.hideDropdown);
                    },
                    beforeDestroy: function () {
                        document.removeEventListener("click", this.hideDropdown), document.removeEventListener("keyup", this.hideDropdown);
                    },
                    methods: {
                        hideAllDropdown: function () {
                            var e = this;
                            y(document.querySelectorAll(".dropdown-menu.show")).forEach(function (t) {
                                e.toggleResponsiveness(t, !1), t.classList.remove("show");
                            });
                        },
                        hideDropdown: function (e) {
                            var t = e.target,
                                n = 27 === e.keyCode,
                                r = "function" === typeof t.closest ? t.closest(".dropdown-switch") : null;
                            (n || (t && !r)) && this.hideAllDropdown();
                        },
                        toggleResponsiveness: function (e, t) {
                            var n = e.closest(".table-responsive");
                            n && (t ? (n.style.overflow = "visible") : n.removeAttribute("style"));
                        },
                        toggleDropdown: function (e, t) {
                            var n = e.target,
                                r = n.closest(".dropdown, .dropup");
                            this.hideAllDropdown(), this.toggleResponsiveness(n, !0), r && r.querySelector(".dropdown-menu").classList.toggle("show", t);
                        },
                    },
                },
                w = n(12),
                C = n.n(w),
                k = n(22);
            function S(e) {
                return (
                    (function (e) {
                        if (Array.isArray(e)) return j(e);
                    })(e) ||
                    (function (e) {
                        if ("undefined" !== typeof Symbol && Symbol.iterator in Object(e)) return Array.from(e);
                    })(e) ||
                    (function (e, t) {
                        if (!e) return;
                        if ("string" === typeof e) return j(e, t);
                        var n = Object.prototype.toString.call(e).slice(8, -1);
                        "Object" === n && e.constructor && (n = e.constructor.name);
                        if ("Map" === n || "Set" === n) return Array.from(e);
                        if ("Arguments" === n || /^(?:Ui|I)nt(?:8|16|32)(?:Clamped)?Array$/.test(n)) return j(e, t);
                    })(e) ||
                    (function () {
                        throw new TypeError("Invalid attempt to spread non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method.");
                    })()
                );
            }
            function j(e, t) {
                (null == t || t > e.length) && (t = e.length);
                for (var n = 0, r = new Array(t); n < t; n++) r[n] = e[n];
                return r;
            }
            function A(e, t, n, r, a, i, o) {
                try {
                    var s = e[i](o),
                        c = s.value;
                } catch (l) {
                    return void n(l);
                }
                s.done ? t(c) : Promise.resolve(c).then(r, a);
            }
            var E = {
                    data: function () {
                        return { page: 2, next: !0, comments: [], moreComments: [] };
                    },
                    methods: {
                        prependComment: function (e) {
                            this.comments.unshift({ template: e });
                        },
                        loadComments: function (e) {
                            var t,
                                n = this;
                            return ((t = C.a.mark(function t() {
                                var r, a, i, o, s, c;
                                return C.a.wrap(function (t) {
                                    for (;;)
                                        switch ((t.prev = t.next)) {
                                            case 0:
                                                return (r = e.target).classList.add("disabled"), (a = r.getAttribute("data-url").replace("page=2", "page=".concat(n.page))), (t.next = 5), k.a.get(a.toString()).text();
                                            case 5:
                                                (i = t.sent),
                                                    (o = new DOMParser()),
                                                    (s = o.parseFromString(i, "text/html")),
                                                    (c = S(s.querySelectorAll("mk-comment"))).forEach(function (e) {
                                                        n.moreComments.push({ template: e.outerHTML });
                                                    }),
                                                    (n.page = n.page + 1),
                                                    (n.next = c.length >= 10),
                                                    r.classList.remove("disabled");
                                            case 13:
                                            case "end":
                                                return t.stop();
                                        }
                                }, t);
                            })),
                            function () {
                                var e = this,
                                    n = arguments;
                                return new Promise(function (r, a) {
                                    var i = t.apply(e, n);
                                    function o(e) {
                                        A(i, r, a, o, s, "next", e);
                                    }
                                    function s(e) {
                                        A(i, r, a, o, s, "throw", e);
                                    }
                                    o(void 0);
                                });
                            })();
                        },
                    },
                },
                O = {
                    mounted: function () {
                        i.a.confirm = this.handleConfirm;
                    },
                    methods: {
                        handleConfirm: function (e, t) {
                            return (
                                this.$bvModal.msgBoxConfirm(e, { okTitle: this.t("dialog.ok"), cancelTitle: this.t("dialog.cancel"), buttonSize: "sm", size: "sm" }).then(function (e) {
                                    var n = i.a.confirm;
                                    e &&
                                        ((i.a.confirm = function () {
                                            return !0;
                                        }),
                                        t.click(),
                                        (i.a.confirm = n));
                                }),
                                !1
                            );
                        },
                    },
                },
                P = n(36),
                L = n(282);
            L.keys().forEach(function (e) {
                var t = L(e),
                    n = e.replace(/^\.\//, "").replace(/\.\w+$/, "");
                o.default.component(n, t.default || t);
            });
            var I = function (e) {
                    e.target.closest(".input-group").querySelector("input").select(), document.execCommand("copy"), e.preventDefault();
                },
                z = {
                    bind: function (e) {
                        e.querySelector("button").addEventListener("click", I);
                    },
                    unbind: function (e) {
                        e.querySelector("button").removeEventListener("click", I);
                    },
                },
                T = function (e, t) {
                    t || (t = ""), (e.closest(".custom-file").querySelector(".custom-file-label").textContent = t.replace(/C:\\fakepath\\/i, ""));
                },
                D = function (e) {
                    var t = e.target;
                    T(t, t.value);
                },
                q = {
                    bind: function (e) {
                        var t = e.querySelector("input");
                        t.addEventListener("change", D), T(t, t.dataset.filename);
                    },
                    unbind: function (e) {
                        e.querySelector("input").removeEventListener("change", D);
                    },
                },
                N = n(126),
                B = n.n(N),
                F = {
                    inserted: function (e, t) {
                        var r = t.modifiers;
                        n.e(4)
                            .then(n.bind(null, 428))
                            .then(function () {
                                $(e)
                                    .find("textarea")
                                    .trumbowyg({
                                        svgPath: B.a,
                                        lang: document.documentElement.lang.toLowerCase().replace("-", "_"),
                                        imageWidthModalEdit: !0,
                                        plugins: { upload: { serverPath: "/images", fileFieldName: "image", urlPropertyName: "url" } },
                                        btnsDef: { image: { dropdown: ["insertImage", "upload"], ico: "insertImage" } },
                                        btns: [
                                            ["viewHTML"],
                                            ["formatting"],
                                            ["strong", "em", "underline"],
                                            ["link"],
                                            ["image"],
                                            ["justifyLeft", "justifyCenter", "justifyRight", "justifyFull"],
                                            ["unorderedList", "orderedList"],
                                            ["foreColor", "backColor"],
                                            r.simple ? null : ["fullscreen"],
                                        ],
                                    })
                                    .on("tbwchange", function () {
                                        this.removeAttribute("maxlength");
                                    });
                            });
                    },
                    unbind: function (e) {
                        $(e).find("textarea").trumbowyg("destroy");
                    },
                };
            function R(e) {
                this.$root.$emit("modal:show", e.currentTarget.href), e.preventDefault();
            }
            var M,
                U = {
                    bind: function (e, t, n) {
                        var r = n.context;
                        e.addEventListener("click", R.bind(r));
                    },
                    unbind: function (e, t, n) {
                        var r = n.context;
                        e.removeEventListener("click", R.bind(r));
                    },
                },
                H = function (e) {
                    var t = e.target,
                        n = t.closest(".d-flex").querySelector("img"),
                        r = t.files[0],
                        a = n.dataset.original;
                    a || (n.dataset.original = n.src), (n.src = r ? URL.createObjectURL(r) : a);
                },
                V = {
                    bind: function (e) {
                        e.addEventListener("change", H);
                    },
                    unbind: function (e) {
                        e.removeEventListener("change", H);
                    },
                },
                W = n(73),
                Y = n(245);
            o.default.directive("money", l.VMoney),
                o.default.directive("copiable", z),
                o.default.directive("file", q),
                o.default.directive("trumbowyg", F),
                o.default.directive("ajax", {
                    bind: function (e, t) {
                        var n = t.arg,
                            r = t.value;
                        e.addEventListener("ajax:".concat(n), r);
                    },
                    unbind: function (e, t) {
                        var n = t.arg,
                            r = t.value;
                        e.removeEventListener("ajax:".concat(n), r);
                    },
                }),
                o.default.directive("modal", U),
                o.default.directive("avatar", V),
                o.default.use(c.a),
                o.default.use(u.VLazyImagePlugin),
                o.default.use(d.a),
                o.default.use(f.a),
                o.default.use(m.a),
                o.default.use(p.a),
                o.default.use(g.a),
                o.default.use(Y.a),
                r.start(),
                i.a.start(),
                W.a.start(),
                n(317),
                document.addEventListener("turbolinks:load", function () {
                    M && M.$destroy(),
                        (M = new o.default({
                            el: ".mount",
                            mixins: [b, x, E, O],
                            data: { file: null, unlimited: Object(P.a)("user_unlimited"), blocked: Object(P.a)("user_blocked"), money: { decimal: ",", thousands: ".", precision: 2 } },
                            computed: {
                                enrollable: function () {
                                    return [this.unlimited, this.blocked].every(function (e) {
                                        return !e;
                                    });
                                },
                            },
                            methods: {
                                submit: function (e) {
                                    var t = e instanceof Event ? e.target.form : e;
                                    i.a.fire(t, "submit");
                                },
                            },
                        }));
                });
        },
        ,
        function (e, t, n) {
            "use strict";
            n.r(t);
            var r = n(64),
                a = n.n(r),
                i = {
                    filters: {
                        format: function (e) {
                            var t = arguments.length > 1 && void 0 !== arguments[1] ? arguments[1] : "#.##0,";
                            return a()(t, e);
                        },
                    },
                    props: { records: { type: Array, required: !0 }, firstHeaderText: { type: String, required: !0 } },
                    data: function () {
                        return {
                            directions: {},
                            sortable: { lesson_statuses_count: this.t("activities_page.chart.lesson_statuses"), comments_count: this.t("activities_page.chart.comments"), ratings_count: this.t("activities_page.chart.ratings") },
                        };
                    },
                    methods: {
                        npsTitle: function (e) {
                            switch (!0) {
                                case 0 === e:
                                    return this.t("activities_table.nps.zero", { score: e });
                                case e >= 76:
                                    return this.t("activities_table.nps.excellence", { score: e });
                                case e >= 51:
                                    return this.t("activities_table.nps.quality", { score: e });
                                case e >= 1:
                                    return this.t("activities_table.nps.improvement", { score: e });
                                default:
                                    return this.t("activities_table.nps.critic", { score: e });
                            }
                        },
                        npsClass: function (e) {
                            switch (!0) {
                                case 0 === e:
                                    return "badge-soft-secondary";
                                case e >= 76:
                                    return "badge-soft-success";
                                case e >= 51:
                                    return "badge-soft-primary";
                                case e >= 1:
                                    return "badge-soft-warning";
                                default:
                                    return "badge-soft-danger";
                            }
                        },
                        sort: function (e) {
                            var t = "desc" === this.directions[e] ? "asc" : "desc";
                            this.$set(this.directions, e, t), this.$emit("sort", { direction: t, column: e });
                        },
                    },
                },
                o = n(7),
                s = Object(o.a)(
                    i,
                    function () {
                        var e = this,
                            t = e.$createElement,
                            n = e._self._c || t;
                        return n("div", { staticClass: "table-responsive" }, [
                            n("table", { staticClass: "table table-sm table-nowrap table-middle card-table" }, [
                                n("thead", [
                                    n(
                                        "tr",
                                        [
                                            n("th", { attrs: { width: "50%" }, domProps: { textContent: e._s(e.firstHeaderText) } }),
                                            e._v(" "),
                                            e._l(e.sortable, function (t, r) {
                                                return n("th", { key: "sortable-" + r }, [
                                                    n("a", {
                                                        staticClass: "text-muted",
                                                        attrs: { href: "#!", "data-sort": "" },
                                                        domProps: { textContent: e._s(t) },
                                                        on: {
                                                            click: function (t) {
                                                                return t.preventDefault(), e.sort(r);
                                                            },
                                                        },
                                                    }),
                                                ]);
                                            }),
                                            e._v(" "),
                                            n("th", { attrs: { width: "5%" } }),
                                            e._v(" "),
                                            n("th", { attrs: { width: "9%" } }),
                                        ],
                                        2
                                    ),
                                ]),
                                e._v(" "),
                                n(
                                    "tbody",
                                    [
                                        e.records.length
                                            ? e._l(e.records, function (t, r) {
                                                  return n("tr", { key: "activity-" + r }, [
                                                      n("td", [e._v(e._s(t.display_name))]),
                                                      e._v(" "),
                                                      n("td", [e._v(e._s(e._f("format")(t.lesson_statuses_count)))]),
                                                      e._v(" "),
                                                      n("td", [e._v(e._s(e._f("format")(t.comments_count)))]),
                                                      e._v(" "),
                                                      n("td", [e._v("\n            " + e._s(e._f("format")(t.ratings_count)) + "\n          ")]),
                                                      e._v(" "),
                                                      n("td", [n("span", { staticClass: "text-muted" }, [e._v("\u2605 " + e._s(e._f("format")(t.average_overall_rating, "#0,0")))])]),
                                                      e._v(" "),
                                                      n("td", [
                                                          n("span", { staticClass: "align-text-base font-weight-bold badge", class: e.npsClass(t.nps_score), attrs: { title: e.npsTitle(t.nps_score) } }, [
                                                              e._v("\n              " + e._s(t.nps_score) + " NPS\n            "),
                                                          ]),
                                                      ]),
                                                  ]);
                                              })
                                            : n("tr", [n("td", { attrs: { colspan: "6" } }, [e._v("\n          " + e._s(e.t("activities_table.no_records")) + "\n        ")])]),
                                    ],
                                    2
                                ),
                            ]),
                        ]);
                    },
                    [],
                    !1,
                    null,
                    null,
                    null
                );
            t.default = s.exports;
        },
        function (e, t, n) {
            "use strict";
            n.r(t);
            var r = n(234),
                a = n(235),
                i = {
                    components: { "draggable-resizable": r.a },
                    props: { record: { type: Object, required: !0 }, maxWidth: { type: Number, default: 1056 }, maxHeight: { type: Number, default: 816 } },
                    data: function () {
                        return {
                            x: this.record.x,
                            y: this.maxHeight - this.record.y,
                            width: this.record.width,
                            content: this.record.content,
                            fontFamily: this.record.font_family,
                            fontColor: this.record.font_color,
                            fontSize: this.record.font_size,
                            preventDeactivation: !1,
                        };
                    },
                    computed: {
                        bottom: function () {
                            return this.maxHeight - this.y;
                        },
                        label: function () {
                            var e = this;
                            return ["full_name", "enrolled_at", "completed_at"].reduce(function (t, n) {
                                return (t[n] = e.t("certificate_text.labels.".concat(n))), t;
                            }, {})[this.content];
                        },
                    },
                    watch: {
                        maxWidth: function () {
                            this.draggable.checkParentSize();
                        },
                        maxHeight: function (e) {
                            this.draggable.checkParentSize(), (this.y = e - this.record.y);
                        },
                    },
                    mounted: function () {
                        (this.draggable = this.$refs.draggable), Object(a.a)(this.content, this.draggable);
                    },
                    methods: {
                        handleResizing: function (e, t, n, r) {
                            (this.x = e), (this.y = t), (this.width = n);
                        },
                        handleDragging: function (e, t) {
                            (this.preventDeactivation = !1), (this.x = e), (this.y = t);
                        },
                        handleActivated: function () {
                            this.$emit("activated", this);
                        },
                        handleDeactivated: function () {
                            this.$emit("deactivated");
                        },
                        attrName: function (e) {
                            return "certificate[texts][][".concat(e, "]");
                        },
                        deselect: function () {
                            this.$refs.draggable && (this.$refs.draggable.enabled = !1);
                        },
                    },
                },
                o = n(7),
                s = Object(o.a)(
                    i,
                    function () {
                        var e = this,
                            t = e.$createElement,
                            n = e._self._c || t;
                        return n(
                            "draggable-resizable",
                            {
                                ref: "draggable",
                                attrs: {
                                    w: e.width,
                                    h: 50,
                                    x: e.x,
                                    y: e.y,
                                    parent: !0,
                                    "max-width": e.maxWidth,
                                    "min-height": 50,
                                    "max-height": 50,
                                    handles: ["tl", "bl", "tr", "br"],
                                    grid: [5, 5],
                                    "prevent-deactivation": e.preventDeactivation,
                                },
                                on: { resizing: e.handleResizing, dragging: e.handleDragging, activated: e.handleActivated, deactivated: e.handleDeactivated },
                            },
                            [
                                n("div", { staticClass: "resize-text" }, [e._v("\n    " + e._s(e.label) + "\n  ")]),
                                e._v(" "),
                                n("input", {
                                    directives: [{ name: "model", rawName: "v-model", value: e.content, expression: "content" }],
                                    attrs: { type: "hidden", name: e.attrName("content") },
                                    domProps: { value: e.content },
                                    on: {
                                        input: function (t) {
                                            t.target.composing || (e.content = t.target.value);
                                        },
                                    },
                                }),
                                e._v(" "),
                                n("input", {
                                    directives: [{ name: "model", rawName: "v-model", value: e.width, expression: "width" }],
                                    attrs: { type: "hidden", name: e.attrName("width") },
                                    domProps: { value: e.width },
                                    on: {
                                        input: function (t) {
                                            t.target.composing || (e.width = t.target.value);
                                        },
                                    },
                                }),
                                e._v(" "),
                                n("input", {
                                    directives: [{ name: "model", rawName: "v-model", value: e.x, expression: "x" }],
                                    attrs: { type: "hidden", name: e.attrName("x") },
                                    domProps: { value: e.x },
                                    on: {
                                        input: function (t) {
                                            t.target.composing || (e.x = t.target.value);
                                        },
                                    },
                                }),
                                e._v(" "),
                                n("input", {
                                    directives: [{ name: "model", rawName: "v-model", value: e.bottom, expression: "bottom" }],
                                    attrs: { type: "hidden", name: e.attrName("y") },
                                    domProps: { value: e.bottom },
                                    on: {
                                        input: function (t) {
                                            t.target.composing || (e.bottom = t.target.value);
                                        },
                                    },
                                }),
                                e._v(" "),
                                n("input", {
                                    directives: [{ name: "model", rawName: "v-model", value: e.fontFamily, expression: "fontFamily" }],
                                    attrs: { type: "hidden", name: e.attrName("font_family") },
                                    domProps: { value: e.fontFamily },
                                    on: {
                                        input: function (t) {
                                            t.target.composing || (e.fontFamily = t.target.value);
                                        },
                                    },
                                }),
                                e._v(" "),
                                n("input", {
                                    directives: [{ name: "model", rawName: "v-model", value: e.fontColor, expression: "fontColor" }],
                                    attrs: { type: "hidden", name: e.attrName("font_color") },
                                    domProps: { value: e.fontColor },
                                    on: {
                                        input: function (t) {
                                            t.target.composing || (e.fontColor = t.target.value);
                                        },
                                    },
                                }),
                                e._v(" "),
                                n("input", {
                                    directives: [{ name: "model", rawName: "v-model", value: e.fontSize, expression: "fontSize" }],
                                    attrs: { type: "hidden", name: e.attrName("font_size") },
                                    domProps: { value: e.fontSize },
                                    on: {
                                        input: function (t) {
                                            t.target.composing || (e.fontSize = t.target.value);
                                        },
                                    },
                                }),
                            ]
                        );
                    },
                    [],
                    !1,
                    null,
                    null,
                    null
                );
            t.default = s.exports;
        },
        function (e, t, n) {
            "use strict";
            n.r(t);
            var r = n(47);
            function a(e, t) {
                var n = Object.keys(e);
                if (Object.getOwnPropertySymbols) {
                    var r = Object.getOwnPropertySymbols(e);
                    t &&
                        (r = r.filter(function (t) {
                            return Object.getOwnPropertyDescriptor(e, t).enumerable;
                        })),
                        n.push.apply(n, r);
                }
                return n;
            }
            function i(e) {
                for (var t = 1; t < arguments.length; t++) {
                    var n = null != arguments[t] ? arguments[t] : {};
                    t % 2
                        ? a(Object(n), !0).forEach(function (t) {
                              o(e, t, n[t]);
                          })
                        : Object.getOwnPropertyDescriptors
                        ? Object.defineProperties(e, Object.getOwnPropertyDescriptors(n))
                        : a(Object(n)).forEach(function (t) {
                              Object.defineProperty(e, t, Object.getOwnPropertyDescriptor(n, t));
                          });
                }
                return e;
            }
            function o(e, t, n) {
                return t in e ? Object.defineProperty(e, t, { value: n, enumerable: !0, configurable: !0, writable: !0 }) : (e[t] = n), e;
            }
            function s(e) {
                return (
                    (function (e) {
                        if (Array.isArray(e)) return c(e);
                    })(e) ||
                    (function (e) {
                        if ("undefined" !== typeof Symbol && Symbol.iterator in Object(e)) return Array.from(e);
                    })(e) ||
                    (function (e, t) {
                        if (!e) return;
                        if ("string" === typeof e) return c(e, t);
                        var n = Object.prototype.toString.call(e).slice(8, -1);
                        "Object" === n && e.constructor && (n = e.constructor.name);
                        if ("Map" === n || "Set" === n) return Array.from(e);
                        if ("Arguments" === n || /^(?:Ui|I)nt(?:8|16|32)(?:Clamped)?Array$/.test(n)) return c(e, t);
                    })(e) ||
                    (function () {
                        throw new TypeError("Invalid attempt to spread non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method.");
                    })()
                );
            }
            function c(e, t) {
                (null == t || t > e.length) && (t = e.length);
                for (var n = 0, r = new Array(t); n < t; n++) r[n] = e[n];
                return r;
            }
            var l = {
                    props: {
                        background: { type: String, required: !0 },
                        initialWidth: { type: Number, required: !0 },
                        initialHeight: { type: Number, required: !0 },
                        fonts: { type: Array, required: !0 },
                        texts: { type: Array, required: !0 },
                    },
                    data: function () {
                        return {
                            open: !1,
                            maxWidth: 1056,
                            maxHeight: 816,
                            imageWidth: this.initialWidth,
                            imageHeight: this.initialHeight,
                            imageSrc: this.background,
                            selected: {},
                            enrolledAt: this.hasContent("enrolled_at"),
                            completedAt: this.hasContent("completed_at"),
                        };
                    },
                    computed: {
                        fontSizes: function () {
                            for (var e = [], t = 1; t <= 11; t++) e.push(Math.round(12 * Math.pow(2, t / 5)));
                            return e;
                        },
                        ratio: function () {
                            return Math.min(this.maxWidth / this.imageWidth, this.maxHeight / this.imageHeight);
                        },
                        width: function () {
                            return parseInt(this.imageWidth * this.ratio, 10);
                        },
                        height: function () {
                            return parseInt(this.imageHeight * this.ratio, 10);
                        },
                        dimensions: function () {
                            return { width: "".concat(this.width, "px"), height: "".concat(this.height, "px") };
                        },
                        unselected: function () {
                            return !("_isVue" in this.selected);
                        },
                        currentTexts: function () {
                            var e = this,
                                t = s(this.texts).filter(function (t) {
                                    var n = t.content,
                                        r = e.enrolledAt && "enrolled_at" === n;
                                    return (e.completedAt && "completed_at" === n) || r || "full_name" === n;
                                }),
                                n = { width: 220, font_family: "Helvetica", font_color: "#000000", font_size: 36 };
                            return (
                                this.enrolledAt && !this.hasContent("enrolled_at") && t.push(i(i({}, n), {}, { content: "enrolled_at", x: 283, y: 240 })),
                                this.completedAt && !this.hasContent("completed_at") && t.push(i(i({}, n), {}, { content: "completed_at", x: 553, y: 240 })),
                                t
                            );
                        },
                    },
                    watch: {
                        imageSrc: {
                            immediate: !0,
                            handler: function (e) {
                                var t = this;
                                e &&
                                    this.getImageDimensions(e, function (e, n) {
                                        (t.imageWidth = e), (t.imageHeight = n);
                                    });
                            },
                        },
                    },
                    mounted: function () {
                        (this.file = document.getElementById("certificate_background")), this.file.addEventListener("change", this.handleFileChanged);
                    },
                    destroyed: function () {
                        this.file.removeEventListener("change", this.handleFileChanged);
                    },
                    methods: {
                        icon: r.a,
                        hasContent: function (e) {
                            return (
                                this.texts.findIndex(function (t) {
                                    return t.content === e;
                                }) > -1
                            );
                        },
                        getImageDimensions: function (e, t) {
                            var n = new Image();
                            (n.onload = function () {
                                t(this.width, this.height);
                            }),
                                (n.src = e);
                        },
                        handleFileChanged: function (e) {
                            var t = e.target.files[0];
                            this.imageSrc = t ? URL.createObjectURL(t) : this.background;
                        },
                        handleSelected: function (e) {
                            "_isVue" in this.selected && ((this.selected.preventDeactivation = !1), this.selected.deselect()), e ? ((this.selected = e), (this.selected.preventDeactivation = !0)) : (this.selected = {});
                        },
                    },
                },
                u = n(7),
                d = Object(u.a)(
                    l,
                    function () {
                        var e = this,
                            t = e.$createElement,
                            n = e._self._c || t;
                        return n(
                            "div",
                            [
                                n("transition", { attrs: { name: "fade" } }, [
                                    n("div", { directives: [{ name: "show", rawName: "v-show", value: e.open, expression: "open" }], staticClass: "position-fixed fixed-top bg-dark" }, [
                                        n("div", { staticClass: "d-flex vh-100" }, [
                                            n("div", { staticClass: "d-flex flex-column justify-content-center align-items-center vh-100 w-100" }, [
                                                n(
                                                    "div",
                                                    { staticClass: "position-relative select-none", style: e.dimensions },
                                                    [
                                                        e._l(e.currentTexts, function (t) {
                                                            return n("mk-certificate-text", {
                                                                key: "text-" + t.content,
                                                                attrs: { record: t, "max-width": e.width, "max-height": e.height },
                                                                on: { activated: e.handleSelected, deactivated: e.handleSelected },
                                                            });
                                                        }),
                                                        e._v(" "),
                                                        n("img", { ref: "image", staticClass: "d-block", style: e.dimensions, attrs: { src: e.imageSrc, draggable: "false", ondragstart: "return false" } }),
                                                    ],
                                                    2
                                                ),
                                            ]),
                                            e._v(" "),
                                            n("div", { staticClass: "bg-white modal-dialog-vertical w-100 border-right" }, [
                                                n("header", { staticClass: "modal-header align-items-center" }, [
                                                    n("h6", { staticClass: "text-sm font-weight-bold text-uppercase modal-title" }, [e._v("\n              " + e._s(e.t("certificate.title")) + "\n            ")]),
                                                ]),
                                                e._v(" "),
                                                n("div", { staticClass: "p-4 border-bottom" }, [
                                                    n("ul", { staticClass: "list-group" }, [
                                                        n("li", { staticClass: "list-group-item d-flex justify-content-between align-items-center text-dark" }, [
                                                            e._v("\n                " + e._s(e.t("certificate.user_name")) + "\n                "),
                                                            n("svg", { staticClass: "feather sm" }, [n("use", { attrs: { "xlink:href": e.icon("eye") } })]),
                                                        ]),
                                                        e._v(" "),
                                                        n(
                                                            "li",
                                                            {
                                                                staticClass: "list-group-item d-flex justify-content-between align-items-center cursor-pointer",
                                                                class: e.enrolledAt ? "text-dark" : "text-muted",
                                                                on: {
                                                                    click: function (t) {
                                                                        t.preventDefault(), (e.enrolledAt = !e.enrolledAt);
                                                                    },
                                                                },
                                                            },
                                                            [
                                                                e._v("\n                " + e._s(e.t("certificate.enrolled_at")) + "\n                "),
                                                                n("svg", { staticClass: "feather sm" }, [n("use", { attrs: { "xlink:href": e.icon(e.enrolledAt ? "eye" : "eye-off") } })]),
                                                            ]
                                                        ),
                                                        e._v(" "),
                                                        n(
                                                            "li",
                                                            {
                                                                staticClass: "list-group-item d-flex justify-content-between align-items-center cursor-pointer",
                                                                class: e.completedAt ? "text-dark" : "text-muted",
                                                                on: {
                                                                    click: function (t) {
                                                                        t.preventDefault(), (e.completedAt = !e.completedAt);
                                                                    },
                                                                },
                                                            },
                                                            [
                                                                e._v("\n                " + e._s(e.t("certificate.completed_at")) + "\n                "),
                                                                n("svg", { staticClass: "feather sm" }, [n("use", { attrs: { "xlink:href": e.icon(e.completedAt ? "eye" : "eye-off") } })]),
                                                            ]
                                                        ),
                                                    ]),
                                                ]),
                                                e._v(" "),
                                                n("div", { staticClass: "p-4 border-bottom" }, [
                                                    n("div", { staticClass: "form-group" }, [
                                                        n("label", { staticClass: "form-control-label" }, [e._v(e._s(e.t("certificate.selected")))]),
                                                        e._v(" "),
                                                        n("h5", { staticClass: "mb-0 text-gray tracking-normal" }, [e._v("\n                " + e._s(e.selected.label || "-") + "\n              ")]),
                                                    ]),
                                                    e._v(" "),
                                                    n("div", { staticClass: "form-group" }, [
                                                        n("label", { staticClass: "form-control-label" }, [e._v(e._s(e.t("certificate.font_family")))]),
                                                        e._v(" "),
                                                        n("div", { staticClass: "form-row" }, [
                                                            n("div", { staticClass: "col-md-10" }, [
                                                                n(
                                                                    "select",
                                                                    {
                                                                        directives: [{ name: "model", rawName: "v-model", value: e.selected.fontFamily, expression: "selected.fontFamily" }],
                                                                        staticClass: "custom-select",
                                                                        attrs: { disabled: e.unselected },
                                                                        on: {
                                                                            change: function (t) {
                                                                                var n = Array.prototype.filter
                                                                                    .call(t.target.options, function (e) {
                                                                                        return e.selected;
                                                                                    })
                                                                                    .map(function (e) {
                                                                                        return "_value" in e ? e._value : e.value;
                                                                                    });
                                                                                e.$set(e.selected, "fontFamily", t.target.multiple ? n : n[0]);
                                                                            },
                                                                        },
                                                                    },
                                                                    e._l(e.fonts, function (t) {
                                                                        return n("option", { key: t, domProps: { value: t } }, [e._v("\n                      " + e._s(t) + "\n                    ")]);
                                                                    }),
                                                                    0
                                                                ),
                                                            ]),
                                                            e._v(" "),
                                                            n("div", { staticClass: "col-md-2" }, [
                                                                n("input", {
                                                                    directives: [{ name: "model", rawName: "v-model", value: e.selected.fontColor, expression: "selected.fontColor" }],
                                                                    staticClass: "form-control",
                                                                    attrs: { type: "color", placeholder: "#3355cc", disabled: e.unselected },
                                                                    domProps: { value: e.selected.fontColor },
                                                                    on: {
                                                                        input: function (t) {
                                                                            t.target.composing || e.$set(e.selected, "fontColor", t.target.value);
                                                                        },
                                                                    },
                                                                }),
                                                            ]),
                                                        ]),
                                                    ]),
                                                    e._v(" "),
                                                    n("div", { staticClass: "form-group mb-0" }, [
                                                        n("label", { staticClass: "form-control-label" }, [e._v(e._s(e.t("certificate.font_size")))]),
                                                        e._v(" "),
                                                        n(
                                                            "select",
                                                            {
                                                                directives: [{ name: "model", rawName: "v-model", value: e.selected.fontSize, expression: "selected.fontSize" }],
                                                                staticClass: "custom-select",
                                                                attrs: { disabled: e.unselected },
                                                                on: {
                                                                    change: function (t) {
                                                                        var n = Array.prototype.filter
                                                                            .call(t.target.options, function (e) {
                                                                                return e.selected;
                                                                            })
                                                                            .map(function (e) {
                                                                                return "_value" in e ? e._value : e.value;
                                                                            });
                                                                        e.$set(e.selected, "fontSize", t.target.multiple ? n : n[0]);
                                                                    },
                                                                },
                                                            },
                                                            e._l(e.fontSizes, function (t) {
                                                                return n("option", { key: "size-" + t }, [e._v("\n                  " + e._s(t) + "\n                ")]);
                                                            }),
                                                            0
                                                        ),
                                                    ]),
                                                ]),
                                                e._v(" "),
                                                n("div", { staticClass: "p-4" }, [
                                                    n(
                                                        "button",
                                                        {
                                                            staticClass: "btn btn-outline-dark btn-block",
                                                            attrs: { type: "button" },
                                                            on: {
                                                                click: function (t) {
                                                                    t.preventDefault(), (e.open = !1);
                                                                },
                                                            },
                                                        },
                                                        [e._v("\n              " + e._s(e.t("certificate.close")) + "\n            ")]
                                                    ),
                                                ]),
                                            ]),
                                        ]),
                                    ]),
                                ]),
                                e._v(" "),
                                e.imageSrc
                                    ? n(
                                          "a",
                                          {
                                              staticClass: "btn btn-block btn-white",
                                              attrs: { href: "#" },
                                              on: {
                                                  click: function (t) {
                                                      t.preventDefault(), (e.open = !0);
                                                  },
                                              },
                                          },
                                          [e._v("\n    " + e._s(e.t("certificate.open_editor")) + "\n  ")]
                                      )
                                    : e._e(),
                            ],
                            1
                        );
                    },
                    [],
                    !1,
                    null,
                    null,
                    null
                );
            t.default = d.exports;
        },
        function (e, t, n) {
            "use strict";
            n.r(t);
            var r = {
                    components: { "sketch-picker": n(248).a },
                    props: {
                        inputName: { type: String, required: !0 },
                        labelClass: {
                            type: [Object, Array],
                            default: function () {
                                return [];
                            },
                        },
                        value: { type: String, required: !0 },
                        defaultColor: { type: String, required: !0 },
                    },
                    data: function () {
                        return { color: this.value || this.defaultColor, open: !1 };
                    },
                    methods: {
                        handleClick: function () {
                            var e = this.open;
                            this.$emit("before-open"), (this.open = !e);
                        },
                        handleUpdate: function (e) {
                            (this.color = e.hex), this.$emit("update", { color: e });
                        },
                    },
                },
                a = n(7),
                i = Object(a.a)(
                    r,
                    function () {
                        var e = this,
                            t = e.$createElement,
                            n = e._self._c || t;
                        return n(
                            "label",
                            { staticClass: "cursor-pointer mb-0", class: e.labelClass },
                            [
                                n("div", { staticClass: "colorpicker", class: { active: e.open }, style: { backgroundColor: e.color }, on: { click: e.handleClick } }),
                                e._v(" "),
                                n(
                                    "transition",
                                    { attrs: { name: "fade" } },
                                    [
                                        e.open
                                            ? n("sketch-picker", {
                                                  attrs: { "disable-alpha": !0, "preset-colors": [] },
                                                  on: { input: e.handleUpdate },
                                                  model: {
                                                      value: e.color,
                                                      callback: function (t) {
                                                          e.color = t;
                                                      },
                                                      expression: "color",
                                                  },
                                              })
                                            : e._e(),
                                    ],
                                    1
                                ),
                                e._v(" "),
                                n("input", { attrs: { type: "hidden", name: e.inputName }, domProps: { value: e.color } }),
                            ],
                            1
                        );
                    },
                    [],
                    !1,
                    null,
                    null,
                    null
                );
            t.default = i.exports;
        },
        function (e, t, n) {
            "use strict";
            n.r(t);
            var r = {
                    props: {
                        url: { type: String, required: !0 },
                        classroomId: { type: Number, required: !0 },
                        lessonId: { type: Number, default: null },
                        parentId: { type: Number, default: null },
                        avatarUrl: { type: String, default: null },
                    },
                    data: function () {
                        return { content: null, invalid: !1, loading: !1 };
                    },
                    computed: {
                        placeholder: function () {
                            return this.t(this.parentId ? "comment_form.leave_reply" : "comment_form.leave_comment");
                        },
                        submitText: function () {
                            return this.t(this.loading ? "comment_form.publishing" : "comment_form.publish");
                        },
                        cancelText: function () {
                            return this.t("comment_form.cancel");
                        },
                    },
                    methods: {
                        handleBeforeSend: function () {
                            this.loading = !0;
                        },
                        handleAjaxSuccess: function (e) {
                            var t = e.detail;
                            this.$emit("created", t[2].responseText), (this.content = ""), (this.invalid = !1), (this.loading = !1);
                        },
                        handleError: function () {
                            (this.invalid = !0), (this.loading = !1);
                        },
                    },
                },
                a = n(7),
                i = Object(a.a)(
                    r,
                    function () {
                        var e = this,
                            t = e.$createElement,
                            n = e._self._c || t;
                        return n("div", { staticClass: "row align-items-start mt-3" }, [
                            e.parentId ? e._e() : n("div", { staticClass: "col-auto" }, [n("div", { staticClass: "avatar" }, [n("img", { staticClass: "avatar-img rounded-circle", attrs: { src: e.avatarUrl } })])]),
                            e._v(" "),
                            n("div", { staticClass: "col text-right", class: { "ml-n2": !e.parentId } }, [
                                n(
                                    "form",
                                    {
                                        directives: [
                                            { name: "ajax", rawName: "v-ajax:beforeSend", value: e.handleBeforeSend, expression: "handleBeforeSend", arg: "beforeSend" },
                                            { name: "ajax", rawName: "v-ajax:success", value: e.handleAjaxSuccess, expression: "handleAjaxSuccess", arg: "success" },
                                            { name: "ajax", rawName: "v-ajax:error", value: e.handleError, expression: "handleError", arg: "error" },
                                        ],
                                        staticClass: "new_comment",
                                        attrs: { action: e.url, "data-remote": "true", method: "post" },
                                    },
                                    [
                                        e.lessonId ? n("input", { attrs: { type: "hidden", name: "comment[lesson_id]" }, domProps: { value: e.lessonId } }) : e._e(),
                                        e._v(" "),
                                        n("input", { attrs: { type: "hidden", name: "comment[classroom_id]" }, domProps: { value: e.classroomId } }),
                                        e._v(" "),
                                        n("input", { attrs: { type: "hidden", name: "comment[parent_id]" }, domProps: { value: e.parentId } }),
                                        e._v(" "),
                                        n("textarea", {
                                            directives: [{ name: "model", rawName: "v-model", value: e.content, expression: "content" }],
                                            staticClass: "form-control mb-2",
                                            class: { "is-invalid": e.invalid },
                                            attrs: { rows: "3", name: "comment[content]", maxlength: "2400", placeholder: e.placeholder },
                                            domProps: { value: e.content },
                                            on: {
                                                input: function (t) {
                                                    t.target.composing || (e.content = t.target.value);
                                                },
                                            },
                                        }),
                                        e._v(" "),
                                        e.parentId
                                            ? n(
                                                  "a",
                                                  {
                                                      staticClass: "btn btn-sm btn-link text-muted",
                                                      attrs: { href: "#" },
                                                      on: {
                                                          click: function (t) {
                                                              return t.preventDefault(), e.$emit("cancel");
                                                          },
                                                      },
                                                  },
                                                  [e._v("\n        " + e._s(e.cancelText) + "\n      ")]
                                              )
                                            : e._e(),
                                        e._v(" "),
                                        n("input", { staticClass: "btn btn-sm btn-success", attrs: { type: "submit", disabled: e.loading }, domProps: { value: e.submitText } }),
                                    ]
                                ),
                            ]),
                        ]);
                    },
                    [],
                    !1,
                    null,
                    null,
                    null
                );
            t.default = i.exports;
        },
        function (e, t, n) {
            "use strict";
            n.r(t);
            var r = {
                    data: function () {
                        return { files: [] };
                    },
                    mounted: function () {
                        addEventListener("direct-upload:initialize", this.handleInitialize),
                            addEventListener("direct-upload:start", this.handleStart),
                            addEventListener("direct-upload:progress", this.handleProgress),
                            addEventListener("direct-upload:error", this.handleError),
                            addEventListener("direct-upload:end", this.handleEnd);
                    },
                    destroyed: function () {
                        removeEventListener("direct-upload:initialize", this.handleInitialize),
                            removeEventListener("direct-upload:start", this.handleStart),
                            removeEventListener("direct-upload:progress", this.handleProgress),
                            removeEventListener("direct-upload:error", this.handleError),
                            removeEventListener("direct-upload:end", this.handleEnd);
                    },
                    methods: {
                        fileClass: function (e) {
                            return ["direct-upload", e.pending ? "direct-upload--pending" : null, e.error ? "direct-upload--error" : null];
                        },
                        find: function (e) {
                            var t = this.files.findIndex(function (t) {
                                return t.id === e;
                            });
                            return { index: t, file: this.files[t] };
                        },
                        handleInitialize: function (e) {
                            var t = e.detail,
                                n = { id: t.id, name: t.file.name, progress: 0, pending: !0, error: !1, complete: !0 };
                            this.files.push(n), this.$emit("initialize", n);
                        },
                        handleStart: function (e) {
                            var t = e.detail.id,
                                n = this.find(t),
                                r = n.index,
                                a = n.file;
                            (a.pending = !1), this.$set(this.files, r, a), this.$emit("start", a);
                        },
                        handleProgress: function (e) {
                            var t = e.detail,
                                n = t.id,
                                r = t.progress,
                                a = this.find(n),
                                i = a.index,
                                o = a.file;
                            (o.progress = r), this.$set(this.files, i, o), this.$emit("progress", o);
                        },
                        handleError: function (e) {
                            e.preventDefault();
                            var t = e.detail,
                                n = t.id,
                                r = t.error,
                                a = this.find(n),
                                i = a.index,
                                o = a.file;
                            (o.error = r), this.$set(this.files, i, o), this.$emit("error", o);
                        },
                        handleEnd: function (e) {
                            var t = e.detail.id,
                                n = this.find(t),
                                r = n.index,
                                a = n.file;
                            (a.complete = !0), this.$set(this.files, r, a), this.$emit("end", a);
                        },
                    },
                },
                a = n(7),
                i = Object(a.a)(
                    r,
                    function () {
                        var e = this,
                            t = e.$createElement,
                            n = e._self._c || t;
                        return n(
                            "div",
                            e._l(e.files, function (t, r) {
                                return n("div", { key: "file-" + r, class: e.fileClass(t) }, [
                                    n("div", { staticClass: "direct-upload__progress", style: "width: " + t.progress + "%", attrs: { title: t.error } }),
                                    e._v(" "),
                                    n("span", { staticClass: "direct-upload__filename" }, [e._v(e._s(t.name))]),
                                ]);
                            }),
                            0
                        );
                    },
                    [],
                    !1,
                    null,
                    null,
                    null
                );
            t.default = i.exports;
        },
        function (e, t, n) {
            "use strict";
            n.r(t);
            var r = {
                    props: { name: { type: String, required: !0 }, src: { type: String, required: !0 } },
                    data: function () {
                        return { imageSrc: this.src };
                    },
                    computed: {
                        htmlFor: function () {
                            return this.name
                                .replace(/[^A-Za-z0-9]/g, "_")
                                .replace(/_{2,}/g, "_")
                                .replace(/_$/g, "");
                        },
                        none: function () {
                            return !this.imageSrc;
                        },
                        labelClass: function () {
                            return ["cursor-pointer", "mb-3", "border-2", this.none ? "card card-inactive" : "rounded bg-light border border-light"];
                        },
                    },
                    methods: {
                        handleChange: function (e) {
                            var t = e.target.files[0];
                            this.imageSrc = t ? URL.createObjectURL(t) : this.src;
                        },
                    },
                },
                a = n(7),
                i = Object(a.a)(
                    r,
                    function () {
                        var e = this,
                            t = e.$createElement,
                            n = e._self._c || t;
                        return n("label", { class: e.labelClass, staticStyle: { "--aspect-ratio": "2" }, attrs: { for: e.htmlFor } }, [
                            n(
                                "div",
                                { staticClass: "d-flex align-items-center justify-content-center text-center text-muted small", class: { "px-4": e.none } },
                                [e.none ? e._t("default") : e._e(), e._v(" "), e.none ? e._e() : n("img", { staticClass: "rounded mh-100 mw-100", attrs: { src: e.imageSrc } })],
                                2
                            ),
                            e._v(" "),
                            n("input", { staticClass: "d-none", attrs: { id: e.htmlFor, name: e.name, type: "file", accept: ".jpg, .jpeg, .png" }, on: { change: e.handleChange } }),
                        ]);
                    },
                    [],
                    !1,
                    null,
                    null,
                    null
                );
            t.default = i.exports;
        },
        function (e, t, n) {
            "use strict";
            n.r(t);
            var r = n(47),
                a = {
                    props: { label: { type: String, required: !0 }, name: { type: String, required: !0 }, value: { type: String, required: !0 } },
                    data: function () {
                        return { selected: "unassigned" === this.value ? "any" : this.value };
                    },
                    computed: {
                        any: function () {
                            return "any" === this.selected;
                        },
                        all: function () {
                            return "all" === this.selected;
                        },
                    },
                    methods: {
                        icon: r.a,
                        toggle: function () {
                            this.selected = this.any ? "all" : "any";
                        },
                    },
                },
                i = n(7),
                o = Object(i.a)(
                    a,
                    function () {
                        var e = this,
                            t = e.$createElement,
                            n = e._self._c || t;
                        return n("label", { staticClass: "cursor-pointer", on: { click: e.toggle } }, [
                            e._v("\n  " + e._s(e.label) + "\n  "),
                            n("span", { staticClass: "text-upcase" }, [e._v(e._s(e.t(e.any ? "label_toggle.any" : "label_toggle.all")))]),
                            e._v(" "),
                            n("svg", { staticClass: "feather sm" }, [n("use", { attrs: { "xlink:href": e.icon("chevron-down") } })]),
                            e._v(" "),
                            n("input", { attrs: { type: "hidden", name: e.name }, domProps: { value: e.selected } }),
                        ]);
                    },
                    [],
                    !1,
                    null,
                    null,
                    null
                );
            t.default = o.exports;
        },
        function (e, t, n) {
            "use strict";
            n.r(t);
            var r = {
                    props: { record: { type: Object, required: !0 }, classroomIds: { type: Array, required: !0 } },
                    data: function () {
                        var e = this.record.active_classrooms.map(function (e) {
                                return e.id;
                            }),
                            t = this.classroomIds.find(function (t) {
                                return e.includes(t);
                            });
                        return { selected: t || e[0], enabled: void 0 !== t };
                    },
                    computed: {
                        id: function () {
                            return "course-".concat(this.record.id);
                        },
                    },
                },
                a = n(7),
                i = Object(a.a)(
                    r,
                    function () {
                        var e = this,
                            t = e.$createElement,
                            n = e._self._c || t;
                        return n("tr", [
                            n("td", { staticClass: "narrow align-middle" }, [
                                n("div", { staticClass: "custom-control custom-switch" }, [
                                    n("input", {
                                        directives: [{ name: "model", rawName: "v-model", value: e.enabled, expression: "enabled" }],
                                        staticClass: "custom-control-input",
                                        attrs: { id: e.id, type: "checkbox" },
                                        domProps: { checked: Array.isArray(e.enabled) ? e._i(e.enabled, null) > -1 : e.enabled },
                                        on: {
                                            change: function (t) {
                                                var n = e.enabled,
                                                    r = t.target,
                                                    a = !!r.checked;
                                                if (Array.isArray(n)) {
                                                    var i = e._i(n, null);
                                                    r.checked ? i < 0 && (e.enabled = n.concat([null])) : i > -1 && (e.enabled = n.slice(0, i).concat(n.slice(i + 1)));
                                                } else e.enabled = a;
                                            },
                                        },
                                    }),
                                    e._v(" "),
                                    n("label", { staticClass: "custom-control-label", attrs: { for: e.id } }),
                                ]),
                            ]),
                            e._v(" "),
                            n("td", { staticClass: "text-sm font-weight-bold text-muted align-middle" }, [e._v("\n    " + e._s(e.record.name) + "\n  ")]),
                            e._v(" "),
                            n("td", [
                                n(
                                    "select",
                                    {
                                        directives: [{ name: "model", rawName: "v-model", value: e.selected, expression: "selected" }],
                                        staticClass: "custom-select custom-select-sm form-control-flush",
                                        attrs: { name: "membership_level[classroom_ids][]", disabled: !e.enabled },
                                        on: {
                                            change: function (t) {
                                                var n = Array.prototype.filter
                                                    .call(t.target.options, function (e) {
                                                        return e.selected;
                                                    })
                                                    .map(function (e) {
                                                        return "_value" in e ? e._value : e.value;
                                                    });
                                                e.selected = t.target.multiple ? n : n[0];
                                            },
                                        },
                                    },
                                    e._l(e.record.active_classrooms, function (t) {
                                        return n("option", { key: "option-" + t.id, domProps: { value: t.id } }, [e._v("\n        " + e._s(t.name) + "\n      ")]);
                                    }),
                                    0
                                ),
                            ]),
                        ]);
                    },
                    [],
                    !1,
                    null,
                    null,
                    null
                );
            t.default = i.exports;
        },
        function (e, t, n) {
            "use strict";
            n.r(t);
            var r = n(12),
                a = n.n(r),
                i = n(22);
            function o(e, t, n, r, a, i, o) {
                try {
                    var s = e[i](o),
                        c = s.value;
                } catch (l) {
                    return void n(l);
                }
                s.done ? t(c) : Promise.resolve(c).then(r, a);
            }
            var s = {
                    props: { size: { type: String, default: null } },
                    data: function () {
                        return { content: null };
                    },
                    mounted: function () {
                        this.$root.$on("modal:show", this.load);
                    },
                    destroyed: function () {
                        this.$root.$off("modal:show", this.load);
                    },
                    methods: {
                        handleRemote: function (e) {
                            var t = e.detail[0];
                            t.getResponseHeader("content-type").match("javascript") ? this.close() : (this.inject(t.response), e.stopPropagation());
                        },
                        remount: function () {
                            var e = this,
                                t = this.$el.querySelector(".btn.btn-link"),
                                n = this.$el.querySelector("[autofocus]"),
                                r = this.$el.querySelector("form");
                            n && n.focus(),
                                t &&
                                    (t.addEventListener("click", function (t) {
                                        t.preventDefault(), e.close();
                                    }),
                                    r.addEventListener("ajax:complete", this.handleRemote));
                        },
                        inject: function (e) {
                            var t = new DOMParser().parseFromString(e, "text/html").querySelector(".form");
                            (this.content = { template: t.outerHTML }), this.$nextTick(this.remount);
                        },
                        close: function () {
                            this.$bvModal.hide("modal");
                        },
                        load: function (e) {
                            var t,
                                n = this;
                            return ((t = a.a.mark(function t() {
                                var r;
                                return a.a.wrap(function (t) {
                                    for (;;)
                                        switch ((t.prev = t.next)) {
                                            case 0:
                                                return (t.next = 2), i.a.get(e).text();
                                            case 2:
                                                (r = t.sent), n.$bvModal.show("modal"), n.inject(r);
                                            case 5:
                                            case "end":
                                                return t.stop();
                                        }
                                }, t);
                            })),
                            function () {
                                var e = this,
                                    n = arguments;
                                return new Promise(function (r, a) {
                                    var i = t.apply(e, n);
                                    function s(e) {
                                        o(i, r, a, s, c, "next", e);
                                    }
                                    function c(e) {
                                        o(i, r, a, s, c, "throw", e);
                                    }
                                    s(void 0);
                                });
                            })();
                        },
                    },
                },
                c = n(7),
                l = Object(c.a)(
                    s,
                    function () {
                        var e = this,
                            t = e.$createElement,
                            n = e._self._c || t;
                        return n("b-modal", { attrs: { id: "modal", "hide-header": !0, "hide-footer": !0, size: e.size, centered: "", static: "" } }, [n(e.content, { tag: "component" })], 1);
                    },
                    [],
                    !1,
                    null,
                    null,
                    null
                );
            t.default = l.exports;
        },
        function (e, t, n) {
            "use strict";
            n.r(t);
            var r = n(238),
                a = {
                    components: { "b-multiselect": n.n(r).a },
                    props: {
                        name: { type: String, required: !0 },
                        selected: {
                            type: Array,
                            default: function () {
                                return [];
                            },
                        },
                        collection: {
                            type: Array,
                            default: function () {
                                return [];
                            },
                        },
                        taggable: { type: Boolean, default: !1 },
                        placeholder: { type: String, default: "" },
                    },
                    data: function () {
                        var e = this;
                        return {
                            options: this.normalize(this.collection),
                            values: this.normalize(
                                this.taggable
                                    ? this.selected
                                    : this.collection.filter(function (t) {
                                          return e.isSelected(t);
                                      })
                            ),
                        };
                    },
                    methods: {
                        isSelected: function (e) {
                            return this.selected.includes(e.value || e);
                        },
                        normalize: function (e) {
                            return "string" === typeof e[0]
                                ? e.map(function (e) {
                                      return { name: e, value: e };
                                  })
                                : e;
                        },
                        addTag: function (e) {
                            var t = { name: e, value: e };
                            this.values.push(t);
                        },
                    },
                },
                i = n(7),
                o = Object(i.a)(
                    a,
                    function () {
                        var e = this,
                            t = e.$createElement,
                            n = e._self._c || t;
                        return n(
                            "div",
                            [
                                n(
                                    "b-multiselect",
                                    {
                                        attrs: {
                                            options: e.options,
                                            multiple: !0,
                                            "close-on-select": !1,
                                            "clear-on-select": !1,
                                            placeholder: e.placeholder,
                                            "tag-placeholder": "Pressione enter para criar tag",
                                            "hide-selected": !0,
                                            taggable: e.taggable,
                                            "select-label": "",
                                            "selected-label": "",
                                            "deselect-label": "",
                                            "limit-text": function (e) {
                                                return "e mais " + e;
                                            },
                                            label: "name",
                                            "track-by": "value",
                                        },
                                        on: { tag: e.addTag },
                                        model: {
                                            value: e.values,
                                            callback: function (t) {
                                                e.values = t;
                                            },
                                            expression: "values",
                                        },
                                    },
                                    [n("span", { attrs: { slot: "noResult" }, slot: "noResult" }, [e._v("Nenhum resultado encontrado")]), e._v(" "), n("span", { attrs: { slot: "noOptions" }, slot: "noOptions" }, [e._v("Lista vazia")])]
                                ),
                                e._v(" "),
                                e._l(e.values, function (t) {
                                    return n("input", { key: "item-" + t.value, attrs: { name: e.name, type: "hidden" }, domProps: { value: t.value } });
                                }),
                                e._v(" "),
                                0 == e.values.length ? n("input", { attrs: { name: e.name, type: "hidden" } }) : e._e(),
                            ],
                            2
                        );
                    },
                    [],
                    !1,
                    null,
                    null,
                    null
                );
            t.default = o.exports;
        },
        function (e, t, n) {
            "use strict";
            n.r(t);
            var r = n(64),
                a = n.n(r),
                i = {
                    filters: {
                        format: function (e) {
                            var t = arguments.length > 1 && void 0 !== arguments[1] ? arguments[1] : "#.##0,";
                            return a()(t, e);
                        },
                    },
                    props: { records: { type: Array, required: !0 } },
                    data: function () {
                        return { directions: {} };
                    },
                    methods: {
                        sort: function (e) {
                            var t = "desc" === this.directions[e] ? "asc" : "desc";
                            this.$set(this.directions, e, t), this.$emit("sort", { column: e, direction: t });
                        },
                    },
                },
                o = n(7),
                s = Object(o.a)(
                    i,
                    function () {
                        var e = this,
                            t = e.$createElement,
                            n = e._self._c || t;
                        return n("div", { staticClass: "table-responsive" }, [
                            n("table", { staticClass: "table table-sm table-nowrap table-middle card-table" }, [
                                n("thead", [
                                    n("tr", [
                                        n("th", { attrs: { width: "50%" } }, [e._v("\n          " + e._s(e.t("rankings_table.full_name")) + "\n        ")]),
                                        e._v(" "),
                                        n("th", { attrs: { width: "15%" } }, [
                                            n("a", {
                                                staticClass: "text-muted",
                                                attrs: { href: "#!", "data-sort": "" },
                                                domProps: { textContent: e._s(e.t("rankings_table.score")) },
                                                on: {
                                                    click: function (t) {
                                                        return t.preventDefault(), e.sort("score");
                                                    },
                                                },
                                            }),
                                        ]),
                                        e._v(" "),
                                        n("th", [
                                            n("a", {
                                                staticClass: "text-muted",
                                                attrs: { href: "#!", "data-sort": "" },
                                                domProps: { textContent: e._s(e.t("rankings_table.progress")) },
                                                on: {
                                                    click: function (t) {
                                                        return t.preventDefault(), e.sort("progress");
                                                    },
                                                },
                                            }),
                                        ]),
                                    ]),
                                ]),
                                e._v(" "),
                                n(
                                    "tbody",
                                    [
                                        e.records.length
                                            ? e._l(e.records, function (t, r) {
                                                  return n("tr", { key: "ranking-" + r }, [
                                                      n("td", [e._v(e._s(t.display_name))]),
                                                      e._v(" "),
                                                      n("td", [e._v(e._s(e._f("format")(t.score)))]),
                                                      e._v(" "),
                                                      n("td", [
                                                          n("div", { staticClass: "row align-items-center no-gutters" }, [
                                                              n("div", { staticClass: "col-auto" }, [n("span", { staticClass: "mr-2" }, [e._v(e._s(t.progress.toFixed(0)) + "%")])]),
                                                              e._v(" "),
                                                              n("div", { staticClass: "col" }, [
                                                                  n("div", { staticClass: "progress progress-sm" }, [n("div", { staticClass: "progress-bar bg-secondary", style: "width: " + t.progress.toFixed(0) + "%" })]),
                                                              ]),
                                                          ]),
                                                      ]),
                                                  ]);
                                              })
                                            : n("tr", [n("td", { attrs: { colspan: "3" } }, [e._v("\n          " + e._s(e.t("rankings_table.no_records")) + "\n        ")])]),
                                    ],
                                    2
                                ),
                            ]),
                        ]);
                    },
                    [],
                    !1,
                    null,
                    null,
                    null
                );
            t.default = s.exports;
        },
        function (e, t, n) {
            "use strict";
            n.r(t);
            var r = n(34),
                a = n.n(r),
                i = n(241),
                o = {
                    components: { StarRating: n.n(i).a },
                    props: { url: { type: String, required: !0 }, value: { type: Number, required: !0 } },
                    data: function () {
                        return { rating: this.value };
                    },
                    methods: {
                        setRating: function (e) {
                            var t = this;
                            this.$nextTick(function () {
                                a.a.fire(t.$el, "submit");
                            });
                        },
                    },
                },
                s = n(7),
                c = Object(s.a)(
                    o,
                    function () {
                        var e = this,
                            t = e.$createElement,
                            n = e._self._c || t;
                        return n(
                            "form",
                            { staticClass: "mt-n1", attrs: { action: e.url, "data-remote": "true", method: "post" } },
                            [
                                n("input", { attrs: { type: "hidden", name: "_method", value: "put" } }),
                                e._v(" "),
                                n("input", {
                                    directives: [{ name: "model", rawName: "v-model", value: e.rating, expression: "rating" }],
                                    attrs: { type: "hidden", name: "rating[stars]" },
                                    domProps: { value: e.rating },
                                    on: {
                                        input: function (t) {
                                            t.target.composing || (e.rating = t.target.value);
                                        },
                                    },
                                }),
                                e._v(" "),
                                n("star-rating", {
                                    attrs: {
                                        "star-size": 18,
                                        "active-color": "#ffca00",
                                        glow: 1,
                                        "star-points": [12, 2, 15.09, 8.26, 22, 9.27, 17, 14.14, 18.18, 21.02, 12, 17.77, 5.82, 21.02, 7, 14.14, 2, 9.27, 8.91, 8.26, 12, 2],
                                        "show-rating": !1,
                                    },
                                    on: { "rating-selected": e.setRating },
                                    model: {
                                        value: e.rating,
                                        callback: function (t) {
                                            e.rating = t;
                                        },
                                        expression: "rating",
                                    },
                                }),
                            ],
                            1
                        );
                    },
                    [],
                    !1,
                    null,
                    null,
                    null
                );
            t.default = c.exports;
        },
        function (e, t, n) {
            "use strict";
            n.r(t);
            var r = n(12),
                a = n.n(r),
                i = n(22),
                o = n(47);
            function s(e, t, n, r, a, i, o) {
                try {
                    var s = e[i](o),
                        c = s.value;
                } catch (l) {
                    return void n(l);
                }
                s.done ? t(c) : Promise.resolve(c).then(r, a);
            }
            function c(e, t) {
                return (
                    (function (e) {
                        if (Array.isArray(e)) return e;
                    })(e) ||
                    (function (e, t) {
                        if ("undefined" === typeof Symbol || !(Symbol.iterator in Object(e))) return;
                        var n = [],
                            r = !0,
                            a = !1,
                            i = void 0;
                        try {
                            for (var o, s = e[Symbol.iterator](); !(r = (o = s.next()).done) && (n.push(o.value), !t || n.length !== t); r = !0);
                        } catch (c) {
                            (a = !0), (i = c);
                        } finally {
                            try {
                                r || null == s.return || s.return();
                            } finally {
                                if (a) throw i;
                            }
                        }
                        return n;
                    })(e, t) ||
                    (function (e, t) {
                        if (!e) return;
                        if ("string" === typeof e) return l(e, t);
                        var n = Object.prototype.toString.call(e).slice(8, -1);
                        "Object" === n && e.constructor && (n = e.constructor.name);
                        if ("Map" === n || "Set" === n) return Array.from(e);
                        if ("Arguments" === n || /^(?:Ui|I)nt(?:8|16|32)(?:Clamped)?Array$/.test(n)) return l(e, t);
                    })(e, t) ||
                    (function () {
                        throw new TypeError("Invalid attempt to destructure non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method.");
                    })()
                );
            }
            function l(e, t) {
                (null == t || t > e.length) && (t = e.length);
                for (var n = 0, r = new Array(t); n < t; n++) r[n] = e[n];
                return r;
            }
            var u = {
                    props: { value: { type: Array, required: !0 }, collection: { type: Array, required: !0 } },
                    data: function () {
                        var e = this,
                            t = this.collection.find(function (t) {
                                return e.value.includes(t[0]);
                            });
                        return { loading: !0, allRoles: [], selections: {}, roleId: t ? t[0] : null };
                    },
                    computed: {
                        moderator: function () {
                            var e = this,
                                t =
                                    this.collection.find(function (t) {
                                        return c(t, 1)[0] === e.roleId;
                                    }) || [],
                                n = c(t, 2)[1];
                            return !!n && n.indexOf("moderator") > -1;
                        },
                    },
                    watch: {
                        moderator: function (e) {
                            var t = this;
                            Object.keys(this.selections).forEach(function (n) {
                                var r = t.selections[n];
                                (r = null === r || r), (t.selections[n] = e ? r : null);
                            }),
                                e && this.$bvModal.show("roles");
                        },
                    },
                    mounted: function () {
                        this.selections = this.value.reduce(function (e, t) {
                            return (e[t] = !0), e;
                        }, {});
                    },
                    methods: {
                        icon: o.a,
                        handleShow: function () {
                            this.allRoles.length || this.fetchRoles();
                        },
                        noBorder: function (e) {
                            return 0 === e ? "border-0" : null;
                        },
                        domId: function (e) {
                            return "role-".concat(e);
                        },
                        fetchRoles: function () {
                            var e,
                                t = this;
                            return ((e = a.a.mark(function e() {
                                var n;
                                return a.a.wrap(function (e) {
                                    for (;;)
                                        switch ((e.prev = e.next)) {
                                            case 0:
                                                return (e.next = 2), i.a.get("/roles.json").json();
                                            case 2:
                                                (n = e.sent), (t.allRoles = n), (t.loading = !1);
                                            case 5:
                                            case "end":
                                                return e.stop();
                                        }
                                }, e);
                            })),
                            function () {
                                var t = this,
                                    n = arguments;
                                return new Promise(function (r, a) {
                                    var i = e.apply(t, n);
                                    function o(e) {
                                        s(i, r, a, o, c, "next", e);
                                    }
                                    function c(e) {
                                        s(i, r, a, o, c, "throw", e);
                                    }
                                    o(void 0);
                                });
                            })();
                        },
                    },
                },
                d = n(7),
                f = Object(d.a)(
                    u,
                    function () {
                        var e = this,
                            t = e.$createElement,
                            n = e._self._c || t;
                        return n(
                            "div",
                            { staticClass: "roles" },
                            [
                                n("div", { staticClass: "input-group" }, [
                                    n(
                                        "select",
                                        {
                                            directives: [{ name: "model", rawName: "v-model", value: e.roleId, expression: "roleId" }],
                                            staticClass: "custom-select custom-select-sm",
                                            attrs: { id: "user_role_ids", name: "user[role_ids][]" },
                                            on: {
                                                change: function (t) {
                                                    var n = Array.prototype.filter
                                                        .call(t.target.options, function (e) {
                                                            return e.selected;
                                                        })
                                                        .map(function (e) {
                                                            return "_value" in e ? e._value : e.value;
                                                        });
                                                    e.roleId = t.target.multiple ? n : n[0];
                                                },
                                            },
                                        },
                                        [
                                            n("option", { attrs: { value: "" } }),
                                            e._v(" "),
                                            e._l(e.collection, function (t) {
                                                var r = t[0],
                                                    a = t[1];
                                                return n("option", { key: "role-option-" + r, domProps: { value: r, textContent: e._s(e.t("roles." + a)) } });
                                            }),
                                        ],
                                        2
                                    ),
                                    e._v(" "),
                                    n("div", { staticClass: "input-group-append" }, [
                                        n(
                                            "button",
                                            {
                                                directives: [{ name: "b-modal", rawName: "v-b-modal.roles", modifiers: { roles: !0 } }],
                                                staticClass: "btn btn-outline-secondary btn-sm",
                                                attrs: { disabled: !e.moderator, type: "button" },
                                                on: {
                                                    click: function (e) {
                                                        e.preventDefault();
                                                    },
                                                },
                                            },
                                            [n("svg", { staticClass: "feather sm" }, [n("use", { attrs: { "xlink:href": e.icon("zoom-in") } })])]
                                        ),
                                    ]),
                                ]),
                                e._v(" "),
                                n(
                                    "b-modal",
                                    {
                                        attrs: { id: "roles", "hide-header": "", "hide-footer": e.loading, "ok-title": e.t("roles.apply"), "ok-only": "", "button-size": "sm", centered: "", scrollable: "", static: "" },
                                        on: { show: e.handleShow },
                                    },
                                    [
                                        e.loading
                                            ? n("div", { staticClass: "d-flex align-items-center justify-content-center", staticStyle: { height: "350px" } }, [
                                                  n("div", { staticClass: "spinner-grow", staticStyle: { width: "3rem", height: "3rem" }, attrs: { role: "status" } }, [n("span", { staticClass: "sr-only" }, [e._v("Loading...")])]),
                                              ])
                                            : n("table", { staticClass: "table table-sm mb-0" }, [
                                                  n(
                                                      "tbody",
                                                      e._l(e.allRoles, function (t, r) {
                                                          return n("tr", { key: "role-" + r }, [
                                                              n("td", { class: e.noBorder(r) }, [
                                                                  e.selections[t.id] ? n("input", { attrs: { type: "hidden", name: "user[role_ids][]" }, domProps: { value: t.id } }) : e._e(),
                                                                  e._v(" "),
                                                                  n("div", { staticClass: "custom-control custom-switch" }, [
                                                                      n("input", {
                                                                          directives: [{ name: "model", rawName: "v-model", value: e.selections[t.id], expression: "selections[item.id]" }],
                                                                          staticClass: "custom-control-input",
                                                                          attrs: { id: e.domId(t.id), type: "checkbox" },
                                                                          domProps: { checked: Array.isArray(e.selections[t.id]) ? e._i(e.selections[t.id], null) > -1 : e.selections[t.id] },
                                                                          on: {
                                                                              change: function (n) {
                                                                                  var r = e.selections[t.id],
                                                                                      a = n.target,
                                                                                      i = !!a.checked;
                                                                                  if (Array.isArray(r)) {
                                                                                      var o = e._i(r, null);
                                                                                      a.checked ? o < 0 && e.$set(e.selections, t.id, r.concat([null])) : o > -1 && e.$set(e.selections, t.id, r.slice(0, o).concat(r.slice(o + 1)));
                                                                                  } else e.$set(e.selections, t.id, i);
                                                                              },
                                                                          },
                                                                      }),
                                                                      e._v(" "),
                                                                      n("label", { staticClass: "custom-control-label", attrs: { for: e.domId(t.id) } }),
                                                                  ]),
                                                              ]),
                                                              e._v(" "),
                                                              n("td", { staticClass: "text-sm font-weight-bold text-muted align-middle", class: e.noBorder(r) }, [e._v("\n            " + e._s(t.course_name) + "\n          ")]),
                                                              e._v(" "),
                                                              n("td", { staticClass: "text-muted align-middle", class: e.noBorder(r) }, [e._v("\n            " + e._s(t.classroom_name) + "\n          ")]),
                                                          ]);
                                                      }),
                                                      0
                                                  ),
                                              ]),
                                    ]
                                ),
                            ],
                            1
                        );
                    },
                    [],
                    !1,
                    null,
                    null,
                    null
                );
            t.default = f.exports;
        },
        function (e, t, n) {
            "use strict";
            n.r(t);
            var r = n(242),
                a = n.n(r),
                i = n(243),
                o = n(47),
                s = {
                    components: { FlatPickr: a.a },
                    props: { record: { type: Object, required: !0 }, courses: { type: Array, required: !0 }, index: { type: Number, required: !0 } },
                    data: function () {
                        var e = this,
                            t = this.customParseDate(this.record.start_at),
                            n = this.customParseDate(this.record.end_at),
                            r = this.record.finished_course_id,
                            a = { enableTime: !0, monthSelectorType: "static" },
                            o = ["immediate", "fixed", "perpetual", "gamified", "hidden", "blocked", "trail"].reduce(function (t, n) {
                                return (t[n] = e.t("schedule.rules.".concat(n))), t;
                            }, {});
                        return (
                            "pt-BR" === document.documentElement.lang &&
                                ((a.dateFormat = "d/m/Y H:i"),
                                (a.locale = i.Portuguese),
                                (a.parseDate = function (t) {
                                    return e.customParseDate(flatpickr.parseDate(t, "d/m/Y H:i"));
                                })),
                            { startAt: t, endAt: n, finishedCourseId: r, rules: o, rule: this.record.rule, deleted: !1, configs: { start: Object.assign({ maxDate: n }, a), end: Object.assign({ minDate: t }, a) } }
                        );
                    },
                    methods: {
                        icon: o.a,
                        attrName: function (e) {
                            return "classroom[schedules_attributes][".concat(this.index, "][").concat(e, "]");
                        },
                        customParseDate: function (e) {
                            if (e) {
                                var t = document.querySelector("meta[name=time-zone]").content;
                                return spacetime(e).goto(t).d;
                            }
                        },
                        handleStartChange: function (e, t, n) {
                            this.$set(this.configs.end, "minDate", t);
                        },
                        handleEndChange: function (e, t, n) {
                            this.$set(this.configs.start, "maxDate", t);
                        },
                    },
                },
                c = n(7),
                l = Object(c.a)(
                    s,
                    function () {
                        var e = this,
                            t = e.$createElement,
                            n = e._self._c || t;
                        return n("tr", [
                            n("td", { staticClass: "py-3 text-sm font-weight-bold text-muted align-middle", class: { "halted-max": e.deleted } }, [e._v("\n    " + e._s(e.record.concatenated_name) + "\n  ")]),
                            e._v(" "),
                            n("td", { staticClass: "border-left border-light", class: { "halted-max": e.deleted } }, [
                                n("input", { attrs: { type: "hidden", name: e.attrName("id") }, domProps: { value: e.record.id } }),
                                e._v(" "),
                                n("input", { attrs: { type: "hidden", name: e.attrName("schedulable_id") }, domProps: { value: e.record.schedulable_id } }),
                                e._v(" "),
                                n("input", { attrs: { type: "hidden", name: e.attrName("schedulable_type") }, domProps: { value: e.record.schedulable_type } }),
                                e._v(" "),
                                n(
                                    "select",
                                    {
                                        directives: [{ name: "model", rawName: "v-model", value: e.rule, expression: "rule" }],
                                        staticClass: "custom-select custom-select-sm form-control-flush",
                                        attrs: { name: e.attrName("rule") },
                                        on: {
                                            change: [
                                                function (t) {
                                                    var n = Array.prototype.filter
                                                        .call(t.target.options, function (e) {
                                                            return e.selected;
                                                        })
                                                        .map(function (e) {
                                                            return "_value" in e ? e._value : e.value;
                                                        });
                                                    e.rule = t.target.multiple ? n : n[0];
                                                },
                                                function (t) {
                                                    e.rule = t.target.value;
                                                },
                                            ],
                                        },
                                    },
                                    e._l(e.rules, function (t, r) {
                                        return n("option", { key: r, domProps: { value: r } }, [e._v("\n        " + e._s(t) + "\n      ")]);
                                    }),
                                    0
                                ),
                            ]),
                            e._v(" "),
                            n("td", { class: { "halted-max": e.deleted } }, [
                                n("div", { directives: [{ name: "show", rawName: "v-show", value: "fixed" == e.rule, expression: "rule == 'fixed'" }], staticClass: "form-row" }, [
                                    n("div", { staticClass: "col-md-6" }, [
                                        n(
                                            "div",
                                            { staticClass: "input-group input-group-sm input-group-merge" },
                                            [
                                                n("flat-pickr", {
                                                    staticClass: "form-control form-control-prepended",
                                                    attrs: { placeholder: e.t("schedule.start_at"), name: e.attrName("start_at"), config: e.configs.start },
                                                    on: { "on-change": e.handleStartChange },
                                                    model: {
                                                        value: e.startAt,
                                                        callback: function (t) {
                                                            e.startAt = t;
                                                        },
                                                        expression: "startAt",
                                                    },
                                                }),
                                                e._v(" "),
                                                n("div", { staticClass: "input-group-prepend" }, [
                                                    n("div", { staticClass: "input-group-text" }, [n("svg", { staticClass: "feather sm" }, [n("use", { attrs: { "xlink:href": e.icon("calendar") } })])]),
                                                ]),
                                            ],
                                            1
                                        ),
                                    ]),
                                    e._v(" "),
                                    n("div", { staticClass: "col-md-6" }, [
                                        n(
                                            "div",
                                            { staticClass: "input-group input-group-sm input-group-merge" },
                                            [
                                                n("flat-pickr", {
                                                    staticClass: "form-control form-control-prepended",
                                                    attrs: { placeholder: e.t("schedule.end_at"), name: e.attrName("end_at"), config: e.configs.end },
                                                    on: { "on-change": e.handleEndChange },
                                                    model: {
                                                        value: e.endAt,
                                                        callback: function (t) {
                                                            e.endAt = t;
                                                        },
                                                        expression: "endAt",
                                                    },
                                                }),
                                                e._v(" "),
                                                n("div", { staticClass: "input-group-prepend" }, [
                                                    n("div", { staticClass: "input-group-text" }, [n("svg", { staticClass: "feather sm" }, [n("use", { attrs: { "xlink:href": e.icon("calendar") } })])]),
                                                ]),
                                            ],
                                            1
                                        ),
                                    ]),
                                ]),
                                e._v(" "),
                                n("div", { directives: [{ name: "show", rawName: "v-show", value: "perpetual" == e.rule, expression: "rule == 'perpetual'" }], staticClass: "input-group input-group-sm input-group-merge" }, [
                                    n("input", { staticClass: "form-control form-control-prepended", attrs: { type: "text", name: e.attrName("after_days") }, domProps: { value: e.record.after_days } }),
                                    e._v(" "),
                                    n("div", { staticClass: "input-group-prepend" }, [n("div", { staticClass: "input-group-text" }, [n("svg", { staticClass: "feather sm" }, [n("use", { attrs: { "xlink:href": e.icon("clock") } })])])]),
                                ]),
                                e._v(" "),
                                n("div", { directives: [{ name: "show", rawName: "v-show", value: "gamified" == e.rule, expression: "rule == 'gamified'" }], staticClass: "input-group input-group-sm input-group-merge" }, [
                                    n("input", { staticClass: "form-control form-control-prepended", attrs: { type: "text", name: e.attrName("minimum_score") }, domProps: { value: e.record.minimum_score } }),
                                    e._v(" "),
                                    n("div", { staticClass: "input-group-prepend" }, [n("div", { staticClass: "input-group-text" }, [n("svg", { staticClass: "feather sm" }, [n("use", { attrs: { "xlink:href": e.icon("disc") } })])])]),
                                ]),
                                e._v(" "),
                                n("div", { directives: [{ name: "show", rawName: "v-show", value: "trail" == e.rule, expression: "rule == 'trail'" }] }, [
                                    n(
                                        "select",
                                        {
                                            directives: [{ name: "model", rawName: "v-model", value: e.finishedCourseId, expression: "finishedCourseId" }],
                                            staticClass: "custom-select custom-select-sm",
                                            attrs: { name: e.attrName("finished_course_id"), required: "trail" == e.rule },
                                            on: {
                                                change: function (t) {
                                                    var n = Array.prototype.filter
                                                        .call(t.target.options, function (e) {
                                                            return e.selected;
                                                        })
                                                        .map(function (e) {
                                                            return "_value" in e ? e._value : e.value;
                                                        });
                                                    e.finishedCourseId = t.target.multiple ? n : n[0];
                                                },
                                            },
                                        },
                                        e._l(e.courses, function (t) {
                                            return n("option", { key: t.id, domProps: { value: t.id } }, [e._v("\n          " + e._s(t.name) + "\n        ")]);
                                        }),
                                        0
                                    ),
                                ]),
                            ]),
                            e._v(" "),
                            "Lesson" == e.record.schedulable_type
                                ? n("td", { staticClass: "text-center narrow align-middle" }, [
                                      n(
                                          "button",
                                          {
                                              staticClass: "btn btn-sm btn-white",
                                              class: { "text-danger": e.deleted },
                                              attrs: { type: "button" },
                                              on: {
                                                  click: function (t) {
                                                      t.preventDefault(), (e.deleted = !e.deleted);
                                                  },
                                              },
                                          },
                                          [n("svg", { staticClass: "feather sm" }, [e.deleted ? n("use", { attrs: { "xlink:href": e.icon("trash-2") } }) : n("use", { attrs: { "xlink:href": e.icon("trash") } })])]
                                      ),
                                      e._v(" "),
                                      n("input", { attrs: { type: "hidden", name: e.attrName("_destroy") }, domProps: { value: e.deleted } }),
                                  ])
                                : e._e(),
                        ]);
                    },
                    [],
                    !1,
                    null,
                    null,
                    null
                );
            t.default = l.exports;
        },
        function (e, t, n) {
            "use strict";
            n.r(t);
            var r = n(247),
                a = n(237),
                i = n.n(a),
                o = n(22);
            function s(e) {
                return (
                    (function (e) {
                        if (Array.isArray(e)) return c(e);
                    })(e) ||
                    (function (e) {
                        if ("undefined" !== typeof Symbol && Symbol.iterator in Object(e)) return Array.from(e);
                    })(e) ||
                    (function (e, t) {
                        if (!e) return;
                        if ("string" === typeof e) return c(e, t);
                        var n = Object.prototype.toString.call(e).slice(8, -1);
                        "Object" === n && e.constructor && (n = e.constructor.name);
                        if ("Map" === n || "Set" === n) return Array.from(e);
                        if ("Arguments" === n || /^(?:Ui|I)nt(?:8|16|32)(?:Clamped)?Array$/.test(n)) return c(e, t);
                    })(e) ||
                    (function () {
                        throw new TypeError("Invalid attempt to spread non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method.");
                    })()
                );
            }
            function c(e, t) {
                (null == t || t > e.length) && (t = e.length);
                for (var n = 0, r = new Array(t); n < t; n++) r[n] = e[n];
                return r;
            }
            t.default = {
                props: { sortUrl: { type: String, required: !0 } },
                data: function () {
                    return { dragging: !1 };
                },
                mounted: function () {
                    var e = this,
                        t = s(this.$el.querySelectorAll(".muuri"));
                    (this.grids = t.map(function (t) {
                        return (
                            t.addEventListener("mousedown", function (e) {
                                return e.target === t && e.preventDefault();
                            }),
                            new r.a(t, {
                                dragEnabled: ((a = document.createElement("div")), "touchAction" in a.style ? (n = "touchAction") : "msTouchAction" in a.style && (n = "msTouchAction"), n && (a.style[n] = "pan-y"), "pan-y" === a.style[n]),
                                dragStartPredicate: function () {
                                    return e.dragging;
                                },
                                dragSort: function () {
                                    return e.grids;
                                },
                                dragCssProps: { touchAction: "pan-y", userSelect: "", userDrag: "", tapHighlightColor: "", touchCallout: "", contentZooming: "" },
                                layout: { fillGaps: !0, rounding: !1 },
                            })
                                .on("dragStart", function (e) {
                                    e.getElement().classList.add("muuri-item-draggable");
                                })
                                .on("dragEnd", function (e) {
                                    e.getElement().classList.remove("muuri-item-draggable");
                                })
                                .on("dragReleaseEnd", e.handleDragItemPositioned)
                        );
                        var n, a;
                    })),
                        i()(this.$el).on("progress", function () {
                            e.grids.forEach(function (e) {
                                return e.refreshItems().layout();
                            });
                        });
                },
                methods: {
                    handleDragItemPositioned: function (e) {
                        var t = e.getGrid(),
                            n = t.getElement().dataset.categoryId,
                            r = t.getItems().map(function (e) {
                                return e.getElement().dataset.id;
                            });
                        o.a.patch(this.sortUrl, { json: { category_id: n, items: r } });
                    },
                },
            };
        },
    ],
    [[253, 2, 0, 5]],
]);
//# sourceMappingURL=application-024ff7151c8402b0fed2.chunk.js.map
