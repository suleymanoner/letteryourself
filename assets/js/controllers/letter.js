class Letter {
  static init() {
    $('#add-letter').validate({
      submitHandler(form, event) {
        event.preventDefault();
        const data = Utils.jsonize_form($(form));
        console.log(data);
        if (data.id) {
          Letter.update(data);
        } else {
          Letter.add(data);
        }
      },
    });
    Letter.getAll();
  }

  static getAll() {
    $('#letters-table').DataTable({
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
        url: 'api/person/letter',
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
          render(data, type, row, meta) {
            return `<div style="min-width: 60px;"> <span class="badge">${data}</span><a class="pull-right" style="font-size: 15px; cursor: pointer;" onclick="Letter.openEditLetter(${data})"><i class="fa fa-edit"></i></a> </div>`;
          },
        },
        { data: 'title' },
        { data: 'body' },
        { data: 'send_at' },
      ],
    });
  }

  static openEditLetter(id) {
    RestClient.get(`api/person/letter/receiver/${id}`, (data) => {
      $("#add-letter *[name='receiver_email']").val(data.receiver_email);
      console.log(data);
    });

    RestClient.get(`api/person/letter/${id}`, (data) => {
      Utils.json_to_form('#add-letter', data);
      $('#add-letter-modal').modal('show');
      console.log(data);
    });
  }

  static add(letter) {
    RestClient.post('api/person/letter', letter, (data) => {
      toastr.success('Letter has been created.');
      Letter.getAll();
      $('#add-letter').trigger('reset');
      $('#add-letter-modal').modal('hide');
    });
  }

  static update(letter) {
    RestClient.put(`api/person/letter/${letter.id}`, letter, (data) => {
      toastr.success('Letter has been updated.');
      Letter.getAll();
      $('#add-letter').trigger('reset');
      $("#add-letter *[name='id']").val('');
      $('#add-letter-modal').modal('hide');
    });
  }
}
