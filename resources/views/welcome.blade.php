<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Calendar APP</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,600&display=swap" rel="stylesheet" />

        <!-- Styles -->
        <link href='https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css' rel='stylesheet'>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.1/font/bootstrap-icons.css" rel="stylesheet">

        <style>
            html, body {
                margin: 0;
                padding: 0;
                font-family: Roboto, sans-serif;
                font-size: 14px;
            }

            #calendar {
                margin: 40px 40px;
            }

            .fc-day:hover {
                cursor: pointer;
                background-color: #F1F6F9;
            }

            #eventDetailsLabel {
                border: none;
                font-size: 24px;
            }

            #eventDetailsLabel:focus {
                border: none;
                outline: none;
            }

            #eventDetailsBody {
                border: none;
                width: 100%;
                font-size: 16px;
                resize: none;
            }

            #eventDetailsBody:focus {
                border: none;
                outline: none;
            }

            .finished {
                background-color: green !important;
                border-color: green !important;
            }

            .finished .fc-event-title {
                text-decoration: line-through !important;
                color: white !important;
            }

            .text .fc-event-title {
                font-weight: 700;
            }
        </style>

        <!-- Scripts  -->
        <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.7/index.global.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/@fullcalendar/bootstrap5@6.1.7/index.global.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    </head>
    <body class="antialiased">
        <div class="container-fluid">
            <div class="row">
                <div class="col-9" style="justify-content: center;">
                    <div id="calendar"></div>
                </div>
                <div class="col-3">
                    <div style="margin: 40px 40px;">
                        <div style="margin-bottom: 25px; text-align: center;">
                            <h1 style="font-size: 1.75em"> Create new event </h1>
                        </div>

                        <div style="padding: 15px; border: 1px solid rgb(206, 212, 218); height: 794.5px;">
                            <form id="create-task-form">
                                <div class="mb-3">
                                    <label for="title" class="form-label">Title</label>
                                    <input type="text" class="form-control" id="title">
                                    <div id="titleError" class="form-text" style="color: red;"></div>
                                </div>

                                <div class="mb-3 text-center">
                                    <input type="radio" class="btn-check" name="priority" id="success-outlined" value="1" autocomplete="off" checked>
                                    <label class="btn btn-outline-info" for="success-outlined">Priority Low</label>

                                    <input type="radio" class="btn-check" name="priority" id="warning-outlined" value="2" autocomplete="off">
                                    <label class="btn btn-outline-warning" for="warning-outlined">Priority Medium</label>

                                    <input type="radio" class="btn-check" name="priority" id="danger-outlined" value="3" autocomplete="off">
                                    <label class="btn btn-outline-danger" for="danger-outlined">Priority High</label>
                                </div>

                                <div class="mb-3">
                                    <label for="description" class="form-label">Description</label>
                                    <textarea class="form-control" id="description" rows="5" style="resize: none;"></textarea>
                                </div>
                                <div class="mb-3 row" style="justify-content: center;">
                                    <div class="col-auto">
                                        <input class="form-control col" id="startDate" type="date" />
                                    </div>
                                    <div class="col-auto">
                                        <input class="form-control col" id="endDate" type="date" />
                                    </div>
                                </div>
                                <div id="dateError" class="form-text" style="color: red;"></div>
                                <div class="d-grid gap-2 mt-5">
                                    <button type="submit" class="btn btn-outline-primary">Submit</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal fade" id="eventDetails" tabindex="-1" aria-labelledby="eventDetailsLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <form id="update-task-form">
                        <div class="modal-content">
                            <div class="modal-header">
                                <input id="eventDetailsLabel" id="updatedTitle" style="width: 100%; border: none;"/>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <textarea rows="3" id="eventDetailsBody">

                                </textarea>
                            </div>

                            <hr/>

                            <div class="text-center">
                                <input type="radio" class="btn-check" name="updatedPriority" id="updated-success-outlined" value="1" autocomplete="off">
                                <label class="btn btn-outline-info" for="updated-success-outlined">Priority Low</label>

                                <input type="radio" class="btn-check" name="updatedPriority" id="updated-warning-outlined" value="2" autocomplete="off">
                                <label class="btn btn-outline-warning" for="updated-warning-outlined">Priority Medium</label>

                                <input type="radio" class="btn-check" name="updatedPriority" id="updated-danger-outlined" value="3" autocomplete="off">
                                <label class="btn btn-outline-danger" for="updated-danger-outlined">Priority High</label>
                            </div>

                            <hr/>

                            <div class="mb-3 row" style="justify-content: center;">
                                <div class="col-auto">
                                    <input class="form-control col" id="updatedStartDate" type="date" />
                                </div>
                                <div class="col-auto">
                                    <input class="form-control col" id="updatedEndDate" type="date" />
                                </div>
                            </div>

                            <div class="text-center" hidden id="updatedError">
                                <hr/>
                                <div id="updatedErrorText" class="form-text" style="color: red;" ></div>
                            </div>

                            <input type="hidden" id="hiddenId"/>

                            <div class="modal-footer" style="display: block;">
                                <div class="d-flex">
                                    <div>
                                        <button type="button" class="btn btn-primary" id="updateEventButton">Save changes</button>
                                    </div>

                                    <div class="ms-auto">
                                        <div class="btn-group" role="group" aria-label="Basic example">
                                            <button type="button" class="btn btn-danger" id="deleteEventButton">Delete</button>
                                            <button type="button" class="btn btn-success" id="completeEventButton">Complete</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const Toast = Swal.mixin({
                toast: true,
                position: 'bottom',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true,
                didOpen: (toast) => {
                    toast.addEventListener('mouseenter', Swal.stopTimer)
                    toast.addEventListener('mouseleave', Swal.resumeTimer)
                }
            })

            const eventDetailsModal = new bootstrap.Modal(document.getElementById('eventDetails'), {
                keyboard: true,
            });

            function formatDate(date) {
                var d = new Date(date),
                    month = '' + (d.getMonth() + 1),
                    day = '' + d.getDate(),
                    year = d.getFullYear();

                if (month.length < 2)
                    month = '0' + month;
                if (day.length < 2)
                    day = '0' + day;

                return [year, month, day].join('-');
            }

            const handleEventClick = (event) => {
                eventDetailsModal.toggle();
                const endDate = event.event.end;

                if (endDate) {
                    endDate.setDate(endDate.getDate() - 1);
                }

                document.getElementById('eventDetailsLabel').value = event.event.title;
                document.getElementById('eventDetailsBody').value = event.event.extendedProps.description ?? 'Empty description';
                document.getElementById('updatedStartDate').value = event.event.startStr;
                document.getElementById('updatedEndDate').value = endDate ? formatDate(endDate) : null;

                const priority = event.event.extendedProps.priority;

                switch(priority) {
                    case 1:
                        document.getElementById('updated-success-outlined').checked = true;
                        break;
                    case 2:
                        document.getElementById('updated-warning-outlined').checked = true;
                        break;
                    case 3:
                        document.getElementById('updated-danger-outlined').checked = true;
                        break;
                }

                document.getElementById('hiddenId').value = event.event.id;
            }

            const calendarElement = document.getElementById('calendar');
            const calendar = new FullCalendar.Calendar(calendarElement, {
                themeSystem: 'bootstrap5',
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: ''
                },
                dayMaxEvents: true,
                height: 850,
                dateClick: function (info) {
                    console.log(info);
                },
                firstDay: 1,
                events: '/api/events',
                eventClick: handleEventClick,
            });

            calendar.render();

            const handleCreateTask = (event) => {
                event.preventDefault();

                const $title = document.getElementById('title');
                const $description = document.getElementById('description');
                const $startDate = document.getElementById('startDate');
                const $endDate = document.getElementById('endDate');
                const $priority = Array.prototype.slice.call(document.getElementsByName('priority'), 0).filter((el) => {
                    return el.checked;
                })[0];

                const $titleError = document.getElementById('titleError');
                $titleError.innerText = '';

                const $dateError = document.getElementById('dateError');
                $dateError.innerText = '';

                axios.post('/api/events', {
                    title: $title.value,
                    description: $description.value,
                    startDate: $startDate.value,
                    endDate: $endDate.value,
                    priority: $priority.value,
                }).then(() => {
                    $title.value = '';
                    $description.value = '';
                    $startDate.value = '';
                    $endDate.value = '';
                    document.getElementsByName('priority')[0].checked = true;

                    calendar.refetchEvents();
                }, (event) => {
                    if (event.response.data.property === 1) {
                        $titleError.innerText = event.response.data.error;
                    }

                    if (event.response.data.property === 2) {
                        $dateError.innerText = event.response.data.error;
                    }
                });
            }

            const form = document.getElementById('create-task-form');
            form.addEventListener('submit', handleCreateTask);

            document.getElementById('eventDetailsLabel').addEventListener('input', () => {
                document.getElementById('updatedError').hidden = true;
            });

            document.getElementById('eventDetailsBody').addEventListener('input', () => {
                document.getElementById('updatedError').hidden = true;
            });

            document.getElementById('updatedStartDate').addEventListener('input', () => {
                document.getElementById('updatedError').hidden = true;
            });

            document.getElementById('updatedEndDate').addEventListener('input', () => {
                document.getElementById('updatedError').hidden = true;
            });

            const updateEventButton = document.getElementById('updateEventButton');
            updateEventButton.addEventListener('click', (event) => {
                const $title = document.getElementById('eventDetailsLabel');
                const $description = document.getElementById('eventDetailsBody');
                const $startDate = document.getElementById('updatedStartDate');
                const $endDate = document.getElementById('updatedEndDate');
                const $priority = Array.prototype.slice.call(document.getElementsByName('updatedPriority'), 0).filter((el) => {
                    return el.checked;
                })[0];

                const id = document.getElementById('hiddenId').value;

                const $updatedError = document.getElementById('updatedError');
                $updatedError.hidden = true;

                axios.post('/api/events/' + id, {
                    title: $title.value,
                    description: $description.value,
                    startDate: $startDate.value,
                    endDate: $endDate.value,
                    priority: $priority.value,
                }).then(() => {
                    calendar.refetchEvents();
                    eventDetailsModal.toggle();

                    Toast.fire({
                        icon: 'success',
                        title: 'Event updated successfully'
                    });
                }, (error) => {
                    document.getElementById('updatedErrorText').innerText = error.response.data.error;
                    $updatedError.hidden = false;
                });
            });

            const deleteEventButton = document.getElementById('deleteEventButton');
            deleteEventButton.addEventListener('click', (event) => {
                Swal.fire({
                    title: 'Attention!',
                    text: 'Do you really want delete this event?',
                    icon: 'warning',
                    showConfirmButton: true,
                    showCancelButton: true,
                }).then((result) => {
                    if (result.isConfirmed) {
                        axios.delete('/api/events/' + document.getElementById('hiddenId').value).then(() => {
                            calendar.refetchEvents();
                            eventDetailsModal.toggle();

                            Toast.fire({
                                icon: 'success',
                                title: 'Event deleted successfully'
                            });
                        });
                    }
                });
            });

            const completeEventButton = document.getElementById('completeEventButton');
            completeEventButton.addEventListener('click', () => {
                axios.post('/api/events/' + document.getElementById('hiddenId').value + '/complete').then(() => {
                    calendar.refetchEvents();
                    eventDetailsModal.toggle();

                    Toast.fire({
                        icon: 'success',
                        title: 'Event completed!'
                    });
                });
            });
        });
    </script>
    </body>
</html>
