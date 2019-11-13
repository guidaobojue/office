$(document).ready(function(){

	var date = new Date();
	var d = date.getDate();
	var m = date.getMonth();
	var y = date.getFullYear();

	// alert(new Date(1451577600000));
	var data = [
			{
				title: '全天事项',
				start: new Date(y, m, 1)
			},
			{
				title: '长距离项目1',
				start: new Date(y, m, d-5),
				end: new Date(y, m, d-2)
			},
			{
				id: 999,
				title: '重复项目',
				start: new Date(y, m, d-3, 16, 0),
				allDay: false
			},
			{
				id: 999,
				title: '重复项目',
				start: new Date(y, m, d+4, 16, 0),
				allDay: false
			},
			{
				title: '会议',
				start: new Date(y, m, d, 10, 31),
				allDay: false
			},
			{
				title: '午餐',
				start: new Date(y, m, d, 12, 0),
				end: new Date(y, m, d, 14, 0),
				allDay: false
			},
			{
				title: '生日聚会',
				start: new Date(y, m, d+1, 19, 0),
				end: new Date(y, m, d+1, 22, 30),
				allDay: false
			}
	];


	if($('.calendar').length > 0){
	$('.calendar').fullCalendar({
		header: {
			left: 'prev,next,today',
			center: 'title',
			right: 'month,agendaWeek,agendaDay'
		},
		buttonText:{
			today:'跳转到当天'
		},
		editable: false,
		events: data
	});
}
	
});