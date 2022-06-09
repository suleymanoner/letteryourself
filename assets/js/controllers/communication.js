class Communication {
  static init() {
    Communication.getComm();
  }

  static getComm() {
    $('#communication-table').DataTable({
      processing: true,
      serverSide: true,
      bDestroy: true,
      searching: false,
      ordering: false,
      pagingType: 'simple',
      preDrawCallback(settings) {
        if (settings.aoData.length < settings._iDisplayLength) {
          settings._iRecordsTotal = 0;
          settings._iRecordsDisplay = 0;
        } else {
          settings._iRecordsTotal = 100000;
          settings._iRecordsDisplay = 100000;
        }
        console.log(settings);
      },
      responsive: true,
      language: {
        zeroRecords: 'Nothing found',
        info: 'Showing page _PAGE_',
        infoEmpty: '',
        infoFiltered: '',
      },
      ajax: {
        url: 'api/person/communication',
        type: 'GET',
        beforeSend(xhr) { xhr.setRequestHeader('Authentication', localStorage.getItem('token')); },
        dataSrc(resp) {
          return resp;
        },
        data(d) {
          d.offset = d.start;
          d.limit = d.length;

          delete d.start;
          delete d.columns;
          delete d.length;
          delete d.draw;
          console.log(d);
        },
      },
      columns: [
        {
          data: 'letter_title',
          render(data) {
            return `<div><span class="badge">${data}</div>`;
          },
        },
        { data: 'email' },
      ],
    });
  }
}
