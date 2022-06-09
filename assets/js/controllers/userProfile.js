class UserProfile {
  static init() {
    UserProfile.getProfileDetails();
  }

  static getProfileDetails() {
    $('#profile-table').DataTable({
      processing: true,
      serverSide: true,
      bDestroy: true,
      bInfo: false, // remove showing table pages
      bPaginate: false, // remove previous and next button
      pagingType: 'simple',
      lengthChange: false, // remove showing entries
      searching: false,
      ordering: false,
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
      ajax: {
        url: 'api/person/profile',
        type: 'GET',
        beforeSend(xhr) { xhr.setRequestHeader('Authentication', localStorage.getItem('token')); },
        dataSrc(resp) {
          return resp;
        },
        data(d) {
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
      ],
    });
  }
}
