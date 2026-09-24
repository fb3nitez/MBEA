document.addEventListener("DOMContentLoaded", function () {
    var modal = document.getElementById("template-manage-modal");
    var list = document.getElementById("template-manage-list");
    var templates = (window.PSYCH_DATA || {}).manageTemplates || [];
    var search = document.getElementById("manage-search");
    var type = document.getElementById("manage-type");
    var sort = document.getElementById("manage-sort");
    var tagsToggle = document.getElementById("template-tags-toggle");
    var tagsList = document.getElementById("template-tags-list");
    var tagsCount = document.getElementById("template-tags-count");
    var routes = window.PSYCH_ROUTES || {};
    var deleteRow = null;
    var lastTrigger = null;
    var previousBodyOverflow = "";

    function esc(value) {
        return String(value == null ? "" : value).replace(
            /[&<>"']/g,
            function (c) {
                return {
                    "&": "&amp;",
                    "<": "&lt;",
                    ">": "&gt;",
                    '"': "&quot;",
                    "'": "&#39;",
                }[c];
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
    function favoriteKey(template) {
        return "mbea-template-" + template.type + "-favorite-" + template.id;
    }
    function isFavorite(template) {
        return (
            !!template.favorite ||
            window.localStorage.getItem(favoriteKey(template)) === "1"
        );
    }
    function setFavorite(template, favorite) {
        try {
            if (favorite)
                window.localStorage.setItem(favoriteKey(template), "1");
            else window.localStorage.removeItem(favoriteKey(template));
        } catch (error) {}
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
    function showToast(message) {
        if (typeof window.showToast === "function") window.showToast(message);
    }
    function tagValues() {
        return templates
            .map(function (template) {
                return template.tag;
            })
            .filter(Boolean)
            .filter(function (tag, index, all) {
                return all.indexOf(tag) === index;
            })
            .sort();
    }
    function renderTags() {
        var values = tagValues();
        tagsCount.textContent = values.length;
        tagsList.innerHTML = values.length
            ? values
                  .map(function (tag) {
                      return (
                          '<span class="template-tag-manage"><button type="button" class="template-tag-rename" data-tag="' +
                          esc(tag) +
                          '">' +
                          esc(tag) +
                          ' <i data-feather="edit-2"></i></button><button type="button" class="template-tag-delete" data-tag="' +
                          esc(tag) +
                          '" aria-label="Remove tag ' +
                          esc(tag) +
                          '" title="Remove tag"><i data-feather="x"></i></button></span>'
                      );
                  })
                  .join("")
            : '<span class="template-manage-empty">No tags yet.</span>';
        tagsList
            .querySelectorAll(".template-tag-rename")
            .forEach(function (button) {
                button.addEventListener("click", function () {
                    var next = window.prompt("Rename tag", button.dataset.tag);
                    if (!next || !next.trim()) return;
                    api("/psychiatrist/clinical-template-tags", {
                        method: "PUT",
                        body: JSON.stringify({
                            from: button.dataset.tag,
                            to: next.trim(),
                        }),
                    }).then(function () {
                        window.location.reload();
                    });
                });
            });
        tagsList
            .querySelectorAll(".template-tag-delete")
            .forEach(function (button) {
                button.addEventListener("click", function () {
                    if (!window.confirm("Remove this tag from templates?"))
                        return;
                    api("/psychiatrist/clinical-template-tags", {
                        method: "DELETE",
                        body: JSON.stringify({ tag: button.dataset.tag }),
                    }).then(function () {
                        window.location.reload();
                    });
                });
            });
        if (window.feather) feather.replace();
    }
    function closeDeleteConfirm() {
        if (!deleteRow) return;
        deleteRow = null;
        render();
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
    function render() {
        var query = (search.value || "").toLowerCase();
        var selectedType = type.value;
        var rows = templates.filter(function (template) {
            return (
                (!selectedType || template.type === selectedType) &&
                (!query ||
                    [template.name, template.tag, template.diag, template.desc]
                        .join(" ")
                        .toLowerCase()
                        .indexOf(query) >= 0)
            );
        });
        if (sort.value === "name")
            rows.sort(function (a, b) {
                return String(a.name || "").localeCompare(String(b.name || ""));
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
        renderTags();
        if (!rows.length) {
            list.innerHTML =
                '<div class="template-manage-empty">No templates match your filters.</div>';
            return;
        }
        list.innerHTML =
            '<div class="template-manage-table-head"><span>Template</span><span>Type</span><span>Items</span><span>Updated</span><span>Created by</span><span class="template-manage-actions-heading">Actions</span></div>' +
            rows
                .map(function (template) {
                    var count =
                        template.type === "dx"
                            ? (template.tests || []).length + " tests"
                            : (template.meds || []).length +
                              " medications · " +
                              (template.lifestyle || []).length +
                              " lifestyle";
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
                        esc((template.type || "").toUpperCase()) +
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
        if (window.feather) feather.replace();
    }
    function bindActions() {
        list.querySelectorAll(".manage-favorite").forEach(function (button) {
            button.addEventListener("click", function () {
                var template = templates.find(function (item) {
                    return String(item.id) === button.dataset.id;
                });
                var next = !isFavorite(template);
                button.setAttribute("aria-pressed", String(next));
                button.classList.toggle("is-favorite", next);
                setFavorite(template, next).then(function () {
                    template.favorite = next;
                    refreshSidebar();
                });
            });
        });
        list.querySelectorAll(".manage-edit").forEach(function (button) {
            button.addEventListener("click", function () {
                var template = templates.find(function (item) {
                    return String(item.id) === button.dataset.id;
                });
                if (window.openTemplateModal)
                    window.openTemplateModal(template.type, template);
            });
        });
        list.querySelectorAll(".manage-duplicate").forEach(function (button) {
            button.addEventListener("click", function () {
                api(
                    "/psychiatrist/clinical-templates/" +
                        button.dataset.id +
                        "/duplicate",
                    { method: "POST" },
                ).then(function (result) {
                    templates.push(result.template);
                    render();
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
                        "/psychiatrist/clinical-templates/" + button.dataset.id,
                        { method: "DELETE" },
                    ).then(function () {
                        row.classList.add("is-removing");
                        setTimeout(function () {
                            templates = templates.filter(function (template) {
                                return (
                                    String(template.id) !== button.dataset.id
                                );
                            });
                            deleteRow = null;
                            render();
                            showToast("Template deleted");
                            var targetRow =
                                nextId &&
                                list.querySelector(
                                    '.template-manage-row[data-id="' +
                                        nextId +
                                        '"]',
                                );
                            var target =
                                targetRow && targetRow.querySelector("button");
                            if (target) target.focus();
                        }, 180);
                    });
                });
            },
        );
    }
    function openManage(manageType) {
        templates = (window.PSYCH_DATA || {}).manageTemplates || templates;
        lastTrigger = document.activeElement;
        if (manageType) type.value = manageType;
        modal.classList.remove("hidden");
        previousBodyOverflow = document.body.style.overflow;
        document.body.style.overflow = "hidden";
        render();
        document.getElementById("template-manage-close").focus();
    }
    function closeManage() {
        closeDeleteConfirm();
        modal.classList.add("hidden");
        document.body.style.overflow = previousBodyOverflow;
        if (lastTrigger) lastTrigger.focus();
    }
    window.refreshTemplateManage = function () {
        templates = (window.PSYCH_DATA || {}).manageTemplates || templates;
        render();
    };
    [search, type, sort].forEach(function (element) {
        element.addEventListener("input", render);
        element.addEventListener("change", render);
    });
    tagsToggle.addEventListener("click", function () {
        var expanded = tagsToggle.getAttribute("aria-expanded") === "true";
        tagsToggle.setAttribute("aria-expanded", String(!expanded));
        tagsList.hidden = expanded;
    });
    document
        .querySelectorAll(".template-manage-open")
        .forEach(function (button) {
            button.addEventListener("click", function () {
                openManage(button.dataset.manageType);
            });
        });
    document
        .getElementById("manage-new-template")
        .addEventListener("click", function () {
            if (window.openTemplateModal)
                window.openTemplateModal(type.value || "rx");
        });
    document
        .getElementById("template-manage-close")
        .addEventListener("click", closeManage);
    modal.addEventListener("click", function (event) {
        if (event.target === modal) closeManage();
    });
    document.addEventListener("click", function (event) {
        if (
            deleteRow &&
            !event.target.closest(".template-delete-confirm") &&
            !event.target.closest(".manage-delete")
        )
            closeDeleteConfirm();
    });
    document.addEventListener("keydown", function (event) {
        if (event.key === "Escape" && !modal.classList.contains("hidden")) {
            if (deleteRow) closeDeleteConfirm();
            else closeManage();
        }
    });
    render();
});
