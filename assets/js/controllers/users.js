class Users {
  static init() {
    Users.getAllPersons();
  }

  static getAllPersons() {
    $('#users-table').DataTable({
      processing: true,
      serverSide: true,
      bDestroy: true,
      pagingType: 'simple',
      preDrawCallback(settings) {
        if (settings.aoData.length < settings._iDisplayLength) {
          settings._iRecordsTotal = 0;
          settings._iRecordsDisplay = 0;
        } else {
          settings._iRecordsTotal = 100000;
          settings._iRecordsDisplay = 100000;
        }
      },
      responsive: true,
      language: {
        zeroRecords: 'Nothing found',
        info: 'Showing page _PAGE_',
        infoEmpty: '',
        infoFiltered: '',
      },
      ajax: {
        url: 'api/admin/persons',
        type: 'GET',
        beforeSend(xhr) { xhr.setRequestHeader('Authentication', localStorage.getItem('token')); },
        dataSrc(resp) {
          return resp;
        },
        data(d) {
          d.offset = d.start;
          d.limit = d.length;
          d.search = d.search.value;
          d.order = encodeURIComponent((d.order[0].dir === 'asc' ? '-' : '+') + d.columns[d.order[0].column].data);

          delete d.start;
          delete d.columns;
          delete d.length;
          delete d.draw;
          console.log(d);
        },
      },
      columns: [
        {
          data: 'id',
          render(data) {
            return `<div><span class="badge">${data}</div>`;
          },
        },
        { data: 'name' },
        { data: 'surname' },
        { data: 'email' },
        { data: 'status' },
      ],
    });
  }
}
