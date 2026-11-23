document.addEventListener("DOMContentLoaded", function () {
    var templates = (window.PSYCH_DATA || {}).manageTemplates || [];
    var managers = {};
    var routes = window.PSYCH_ROUTES || {};

    function esc(value) {
        return String(value == null ? "" : value).replace(
            /[&<>"']/g,
            function (character) {
                return {
                    "&": "&amp;",
                    "<": "&lt;",
                    ">": "&gt;",
                    '"': "&quot;",
                    "'": "&#39;",
                }[character];
            },
        );
    }

    function api(url, options) {
        options = options || {};
        options.headers = Object.assign(
            {
                Accept: "application/json",
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": document.querySelector(
                    'meta[name="csrf-token"]',
                ).content,
            },
            options.headers || {},
        );
        return fetch(url, options).then(function (response) {
            return response.json().then(function (body) {
                if (!response.ok)
                    throw new Error(body.message || "Request failed.");
                return body;
            });
        });
    }

    function showToast(message) {
        if (typeof window.showToast === "function") window.showToast(message);
    }

    function templateItems(template, key) {
        var value = template[key] || (template.payload || {})[key] || [];
        return Array.isArray(value) ? value : [];
    }

    function matchesManager(template, managerType) {
        if (managerType === "dx") return template.type === "dx";
        if (template.type !== "rx") return false;
        if (managerType === "lifestyle")
            return templateItems(template, "lifestyle").length > 0;
        return templateItems(template, "meds").length > 0;
    }

    function favoriteKey(template) {
        return "mbea-template-" + template.type + "-favorite-" + template.id;
    }

    function isFavorite(template) {
        try {
            return (
                !!template.favorite ||
                window.localStorage.getItem(favoriteKey(template)) === "1"
            );
        } catch (error) {
            return !!template.favorite;
        }
    }

    function setFavorite(template, favorite) {
        try {
            if (favorite)
                window.localStorage.setItem(favoriteKey(template), "1");
            else window.localStorage.removeItem(favoriteKey(template));
        } catch (error) {
            // The server remains the source of truth when local storage is unavailable.
        }
        var route =
            routes.templatesFavorite ||
            "/psychiatrist/clinical-templates/" + template.id + "/favorite";
        return api(route.replace("__ID__", template.id), {
            method: "PUT",
            body: JSON.stringify({ favorite: favorite }),
        });
    }

    function refreshSidebar() {
        if (typeof window.buildTemplateLibraries === "function")
            window.buildTemplateLibraries();
    }

    function actionButton(className, icon, label, template, pressed) {
        return (
            '<button type="button" class="template-icon-button ' +
            className +
            '" data-id="' +
            template.id +
            '" title="' +
            label +
            '" aria-label="' +
            label +
            " " +
            esc(template.name || "Template") +
            '"' +
            (pressed == null ? "" : ' aria-pressed="' + pressed + '"') +
            '><i data-feather="' +
            icon +
            '"></i></button>'
        );
    }

    function createManager(modal) {
        var managerType = modal.dataset.templateType;
        var list = modal.querySelector(".template-manage-list");
        var search = modal.querySelector(".template-manage-search");
        var sort = modal.querySelector(".template-manage-sort");
        var closeButton = modal.querySelector(".template-manage-close");
        var newButton = modal.querySelector(".template-manage-new");
        var deleteRow = null;
        var lastTrigger = null;
        var previousBodyOverflow = "";

        function scopedTemplates() {
            return templates.filter(function (template) {
                return matchesManager(template, managerType);
            });
        }

        function closeDeleteConfirm() {
            if (!deleteRow) return;
            deleteRow = null;
            render();
        }

        function render() {
            var query = (search.value || "").toLowerCase();
            var rows = scopedTemplates().filter(function (template) {
                return (
                    !query ||
                    [template.name, template.tag, template.diag, template.desc]
                        .join(" ")
                        .toLowerCase()
                        .indexOf(query) >= 0
                );
            });
            if (sort.value === "name")
                rows.sort(function (a, b) {
                    return String(a.name || "").localeCompare(
                        String(b.name || ""),
                    );
                });
            if (sort.value === "updated")
                rows.sort(function (a, b) {
                    return String(b.updated_at || "").localeCompare(
                        String(a.updated_at || ""),
                    );
                });
            if (sort.value === "usage")
                rows.sort(function (a, b) {
                    return (b.usage_count || 0) - (a.usage_count || 0);
                });

            if (!rows.length) {
                list.innerHTML =
                    '<div class="template-manage-empty">No templates found.</div>';
                return;
            }
            list.innerHTML =
                '<div class="template-manage-table-head"><span>Template</span><span>Type</span><span>Items</span><span>Updated</span><span>Created by</span><span class="template-manage-actions-heading">Actions</span></div>' +
                rows
                    .map(function (template) {
                        var meds = templateItems(template, "meds").length;
                        var lifestyle = templateItems(
                            template,
                            "lifestyle",
                        ).length;
                        var count =
                            managerType === "dx"
                                ? templateItems(template, "tests").length +
                                  " tests"
                                : managerType === "lifestyle"
                                  ? lifestyle + " lifestyle interventions"
                                  : meds + " medications";
                        var favorite = isFavorite(template);
                        var actions =
                            deleteRow === String(template.id)
                                ? '<span class="template-delete-confirm"><span>Delete?</span><button type="button" class="template-delete-yes" data-id="' +
                                  template.id +
                                  '">Yes</button><button type="button" class="template-delete-no">Cancel</button></span>'
                                : actionButton(
                                      "manage-favorite",
                                      "star",
                                      favorite ? "Unfavorite" : "Favorite",
                                      template,
                                      favorite,
                                  ) +
                                  actionButton(
                                      "manage-edit",
                                      "edit-2",
                                      "Edit",
                                      template,
                                  ) +
                                  actionButton(
                                      "manage-duplicate",
                                      "copy",
                                      "Duplicate",
                                      template,
                                  ) +
                                  actionButton(
                                      "manage-delete",
                                      "trash-2",
                                      "Delete",
                                      template,
                                  );
                        var typeLabel =
                            managerType === "lifestyle"
                                ? "LIFESTYLE"
                                : managerType.toUpperCase();
                        return (
                            '<div class="template-manage-row" data-id="' +
                            template.id +
                            '"><div><strong>' +
                            esc(template.name) +
                            "</strong><small>" +
                            esc(template.tag || "Uncategorized") +
                            " · " +
                            esc(template.diag || template.desc || "") +
                            "</small></div><span>" +
                            typeLabel +
                            "</span><span>" +
                            count +
                            "</span><span>" +
                            esc((template.updated_at || "").slice(0, 10)) +
                            "</span><span>" +
                            esc(template.creator_name || "System") +
                            '</span><div class="template-manage-actions">' +
                            actions +
                            "</div></div>"
                        );
                    })
                    .join("");
            bindActions();
            if (window.feather) window.feather.replace();
        }

        function bindActions() {
            list.querySelectorAll(".manage-favorite").forEach(function (button) {
                button.addEventListener("click", function () {
                    var template = templates.find(function (item) {
                        return String(item.id) === button.dataset.id;
                    });
                    var next = !isFavorite(template);
                    setFavorite(template, next)
                        .then(function () {
                            template.favorite = next;
                            refreshSidebar();
                            render();
                        })
                        .catch(function (error) {
                            showToast(error.message);
                        });
                });
            });
            list.querySelectorAll(".manage-edit").forEach(function (button) {
                button.addEventListener("click", function () {
                    var template = templates.find(function (item) {
                        return String(item.id) === button.dataset.id;
                    });
                    if (window.openTemplateModal)
                        window.openTemplateModal(
                            managerType === "lifestyle"
                                ? "lifestyle"
                                : template.type,
                            template,
                        );
                });
            });
            list.querySelectorAll(".manage-duplicate").forEach(function (button) {
                button.addEventListener("click", function () {
                    api(
                        "/psychiatrist/clinical-templates/" +
                            button.dataset.id +
                            "/duplicate",
                        { method: "POST" },
                    )
                        .then(function (result) {
                            templates.push(result.template);
                            managers.forEach(function (manager) {
                                manager.render();
                            });
                        })
                        .catch(function (error) {
                            showToast(error.message);
                        });
                });
            });
            list.querySelectorAll(".manage-delete").forEach(function (button) {
                button.addEventListener("click", function () {
                    deleteRow = button.dataset.id;
                    render();
                    var yes = list.querySelector(".template-delete-yes");
                    if (yes) yes.focus();
                });
            });
            list.querySelectorAll(".template-delete-no").forEach(function (button) {
                button.addEventListener("click", closeDeleteConfirm);
            });
            list.querySelectorAll(".template-delete-yes").forEach(
                function (button) {
                    button.addEventListener("click", function () {
                        var row = button.closest(".template-manage-row");
                        var nextId =
                            row.nextElementSibling &&
                            row.nextElementSibling.dataset.id;
                        api(
                            "/psychiatrist/clinical-templates/" +
                                button.dataset.id,
                            { method: "DELETE" },
                        )
                            .then(function () {
                                row.classList.add("is-removing");
                                setTimeout(function () {
                                    templates = templates.filter(function (
                                        template,
                                    ) {
                                        return (
                                            String(template.id) !==
                                            button.dataset.id
                                        );
                                    });
                                    deleteRow = null;
                                    managers.forEach(function (manager) {
                                        manager.render();
                                    });
                                    showToast("Template deleted");
                                    var targetRow =
                                        nextId &&
                                        list.querySelector(
                                            '.template-manage-row[data-id="' +
                                                nextId +
                                                '"]',
                                        );
                                    var target =
                                        targetRow &&
                                        targetRow.querySelector("button");
                                    if (target) target.focus();
                                }, 180);
                            })
                            .catch(function (error) {
                                showToast(error.message);
                            });
                    });
                },
            );
        }

        function open(trigger) {
            templates = (window.PSYCH_DATA || {}).manageTemplates || templates;
            lastTrigger = trigger;
            previousBodyOverflow = document.body.style.overflow;
            openModal(modal.id);
            document.body.style.overflow = "hidden";
            render();
            closeButton.focus();
        }

        function close() {
            closeDeleteConfirm();
            closeModal(modal.id);
            document.body.style.overflow = previousBodyOverflow;
            if (lastTrigger) lastTrigger.focus();
        }

        search.addEventListener("input", render);
        sort.addEventListener("change", render);
        newButton.addEventListener("click", function () {
            if (window.openTemplateModal)
                window.openTemplateModal(
                    managerType === "lifestyle" ? "lifestyle" : managerType,
                );
        });
        closeButton.addEventListener("click", close);
        modal.addEventListener("click", function (event) {
            if (event.target === modal) close();
        });
        document.addEventListener("keydown", function (event) {
            if (event.key === "Escape" && !modal.classList.contains("hidden")) {
                if (deleteRow) closeDeleteConfirm();
                else close();
            }
        });

        render();
        return { open: open, render: render };
    }

    document
        .querySelectorAll(".template-manage-modal")
        .forEach(function (modal) {
            managers[modal.dataset.templateType] = createManager(modal);
        });

    document
        .querySelectorAll(".template-manage-open")
        .forEach(function (button) {
            button.addEventListener("click", function () {
                var manager = managers[button.dataset.manageType];
                if (manager) manager.open(button);
            });
        });

    window.refreshTemplateManage = function () {
        templates = (window.PSYCH_DATA || {}).manageTemplates || templates;
        Object.keys(managers).forEach(function (type) {
            managers[type].render();
        });
    };
});
