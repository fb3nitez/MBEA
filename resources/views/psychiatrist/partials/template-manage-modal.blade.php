<div class="modal-overlay hidden template-manage-modal" id="template-manage-modal-{{ $managerType }}"
  data-template-type="{{ $managerType }}">
  <div class="modal-box template-manage-modal-box" role="dialog" aria-modal="true"
    aria-labelledby="template-manage-title-{{ $managerType }}">
    <div class="modal-header">
      <div>
        <h3 id="template-manage-title-{{ $managerType }}">Manage {{ $managerTitle }} Templates</h3>
      </div>
      <button class="modal-close template-manage-close" aria-label="Close {{ strtolower($managerTitle) }} template management">
        <i data-feather="x"></i>
      </button>
    </div>
    <div class="modal-body template-manage-modal-body">
      <div class="template-manage-head">
        <button type="button" class="btn-blue template-manage-new">
          <i data-feather="plus"></i> New {{ $managerTitle }} Template
        </button>
      </div>
      <div class="template-manage-toolbar card">
        <input class="field-input template-manage-search" type="search" placeholder="Search {{ strtolower($managerTitle) }} templates" />
        <select class="field-input template-manage-sort" aria-label="Sort templates">
          <option value="name">Name</option>
          <option value="updated">Last updated</option>
          <option value="usage">Most used</option>
        </select>
      </div>
      <div class="template-manage-table-scroll">
        <div class="template-manage-table card template-manage-list" aria-live="polite"></div>
      </div>
    </div>
  </div>
</div>
