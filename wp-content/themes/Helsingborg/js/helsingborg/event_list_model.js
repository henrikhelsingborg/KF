function EventModel(data) {
	if (!data) { data = {}; }

	var self = this;
	self.EventID = data.EventID;
	self.Date = data.Date;
	self.Name = data.Name;
	self.Description = data.Description;
	self.ImagePath = data.ImagePath;
	self.Location = data.Location;
	self.EventTypesName = data.EventTypesName;
}

function TypeModel(data) {
	if (!data) { data = {}; }

	var self = this;
	self.ID = data.ID;
	self.Name = data.Name;
}

function EventPageModel(data) {
	if (!data) { data = {}; }

	var self = this;
	self.events = ExtractModels(self, data.events, EventModel);
	self.eventTypes = ExtractModels(self, data.eventTypes, TypeModel);
	self.selectedEventTypes = ko.observable();

	self.change = function() {
		jQuery('#selectedTypes').trigger('change');
	}

	var filters = [
	{
		Type: "text",
		Name: "Namn",
		Value: ko.observable(""),
		RecordValue: function(record) { return record.Name; }
	},
	{
		Type: "text",
		Name: "Plats",
		Value: ko.observable(""),
		RecordValue: function(record) { return (record.Location != null) ? record.Location : ""; }
	},
	{
		Type: "calendar",
		Name: "Startdatum",
		CalendarID: "datetimepickerstart",
		Value: ko.observable(""),
		RecordValue: function(record) { return (record.Date != null) ? record.Date : ""; }
	},
	{
		Type: "calendar",
		Name: "Slutdatum",
		CalendarID: "datetimepickerend",
		Value: ko.observable(""),
		RecordValue: function(record) { return (record.Date != null) ? record.Date : ""; }
	},
	{
		Type: "select",
		Name: "Evenemangstyp",
		Options: self.eventTypes,
		CurrentOption: self.selectedEventTypes,
		RecordValue: function(record) { return (record.EventTypesName != null ) ? record.EventTypesName : ""; }
	}
	];

	self.filter = new FilterModel(filters, self.events);
	self.pager = new PagerModel(self.filter.filteredRecords);
}

function PagerModel(records) {
	var self = this;

	self.records = GetObservableArray(records);
	self.currentPageIndex = ko.observable(self.records().length > 0 ? 0 : -1);
	self.currentPageSize = 7;
	self.recordCount = ko.computed(function() {
		return self.records().length;
	});
	self.maxPageIndex = ko.computed(function() {
		return Math.ceil(self.records().length / self.currentPageSize) - 1;
	});
	self.currentPageRecords = ko.computed(function() {
		var newPageIndex = -1;
		var pageIndex = self.currentPageIndex();
		var maxPageIndex = self.maxPageIndex();
		if (pageIndex > maxPageIndex)
		{
			newPageIndex = maxPageIndex;
		}
		else if (pageIndex == -1)
		{
			if (maxPageIndex > -1)
			{
				newPageIndex = 0;
			}
			else
			{
				newPageIndex = -2;
			}
		}
		else
		{
			newPageIndex = pageIndex;
		}

		if (newPageIndex != pageIndex)
		{
			if (newPageIndex >= -1)
			{
				self.currentPageIndex(newPageIndex);
			}
			return [];
		}

		var pageSize = self.currentPageSize;
		var startIndex = pageIndex * pageSize;
			var endIndex = startIndex + pageSize;
			return self.records().slice(startIndex, endIndex);
		}).extend({ throttle: 5 });
		self.currentStatus = function(index) {
			return (self.currentPageIndex() == index) ? 'current' : '';
		};
		self.isHidden = function(index) {
			return (self.maxPageIndex() >= index) ? true : false;
		}
		self.moveFirst = function() {
			self.changePageIndex(0);
		};
		self.movePrevious = function() {
			self.changePageIndex(self.currentPageIndex() - 1);
		};
		self.moveNext = function() {
			self.changePageIndex(self.currentPageIndex() + 1);
		};
		self.moveLast = function() {
			self.changePageIndex(self.maxPageIndex());
		};
		self.changePageIndex = function(newIndex) {
			if (newIndex < 0
				|| newIndex == self.currentPageIndex()
				|| newIndex > self.maxPageIndex())
				{
					return;
				}
				self.currentPageIndex(newIndex);
			};
			self.onPageSizeChange = function() {
				self.currentPageIndex(0);
			};
			self.renderPagers = function() {
				var pager = '<ul class="pagination" role="menubar" aria-label="Pagination">';
				pager += '<li class="arrow"><a href="#" data-bind="click: pager.movePrevious, enable: pager.currentPageIndex() > 0">&laquo; Föregående</a></li>';
				var max = self.maxPageIndex();
				for (i = 0; i <= max; i++) {
					pager += '<li data-bind="css: pager.currentStatus('+i+'), visible: pager.isHidden('+i+')"><a href="#" data-bind="click: function(data, event) { pager.currentPageIndex('+i+') }">'+(i+1)+'</a></li>';
				}
				pager += '<li class="arrow"><a href="#" data-bind="click: pager.moveNext, enable: pager.currentPageIndex() < pager.maxPageIndex()">Nästa &raquo;</a></li>';
				pager += '</ul>';
				$("div.Pager").html(pager);
			};
			self.renderNoRecords = function() {
				var message = "<span data-bind=\"visible: pager.recordCount() == 0\">Hittade inga event.</span>";
				$("div.NoRecords").html(message);
			};
			self.renderPagers();
			self.renderNoRecords();
}

function FilterModel(filters, records)
											{
												var self = this;
												self.records = GetObservableArray(records);
												self.filters = ko.observableArray(filters);
												self.activeFilters = ko.computed(function() {
													var filters = self.filters();
													var activeFilters = [];
													for (var index = 0; index < filters.length; index++)
														{
															var filter = filters[index];
															if (filter.CurrentOption)
																{
																	var filterOption = filter.CurrentOption();
																	if (filterOption != null)
																		{
																			var activeFilter = {
																				Filter: filter,
																				IsFiltered: function(filter, record)
																				{
																					var filterOption = filter.CurrentOption();
																					if (!filterOption)
																						{
																							return;
																						}

																						var recordValue = filter.RecordValue(record);
																						return filterOption.indexOf(recordValue) == -1;
																					}
																				};
																				activeFilters.push(activeFilter);
																			}
																		}
																		else if (filter.Value)
																			{
																				var filterValue = filter.Value();
																				if (filterValue && filterValue != "" && filterValue != null)
																					{
																						var activeFilter = {
																							Filter: filter,
																							IsFiltered: function(filter, record)
																							{
																								var filterValue = filter.Value();
																								filterValue = filterValue.toUpperCase();

																								var recordValue = filter.RecordValue(record);
																								recordValue = recordValue.toUpperCase();

																								if (filter.Type == "calendar") {
																									var recordDate   = new Date(filterValue);
																									var selectedDate = new Date(recordValue);

																									if (filter.Name.indexOf("Start") > -1 ){
																										return recordDate > selectedDate;
																									}else{
																										return recordDate < selectedDate;
																									}
																								} else {
																									return recordValue.indexOf(filterValue) == -1;
																								}
																							}
																						};
																						activeFilters.push(activeFilter);
																					}
																				}
																			}

																			return activeFilters;
																		});
																		self.filteredRecords = ko.computed(function() {
																			var records = self.records();
																			var filters = self.activeFilters();
																			if (filters.length == 0)
																				{
																					return records;
																				}

																				var filteredRecords = [];
																				for (var rIndex = 0; rIndex < records.length; rIndex++)
																					{
																						var isIncluded = true;
																						var record = records[rIndex];
																						for (var fIndex = 0; fIndex < filters.length; fIndex++)
																							{
																								var filter = filters[fIndex];
																								var isFiltered = filter.IsFiltered(filter.Filter, record);
																								if (isFiltered)
																									{
																										isIncluded = false;
																										break;
																									}
																								}

																								if (isIncluded)
																									{
																										filteredRecords.push(record);
																									}
																								}

																								return filteredRecords;
																							}).extend({ throttle: 200 });
																						}

function ExtractModels(parent, data, constructor)
																						{
																							var models = [];
																							if (data == null)
																								{
																									return models;
																								}

																								for (var index = 0; index < data.length; index++)
																									{
																										var row = data[index];
																										var model = new constructor(row, parent);
																										models.push(model);
																									}

																									return models;
																								}

function GetObservableArray(array)
																								{
																									if (typeof(array) == 'function')
																										{
																											return array;
																										}

																										return ko.observableArray(array);
																									}

function CompareCaseInsensitive(left, right)
																									{
																										if (left == null)
																											{
																												return right == null;
																											}
																											else if (right == null)
																												{
																													return false;
																												}

																												return left.toUpperCase() <= right.toUpperCase();
																											}

function GetOption(name, value, filterValue)
																											{
																												var option = {
																													Name: name,
																													Value: value,
																													FilterValue: filterValue
																												};
																												return option;
																											}

function SortArray(array, direction, comparison)
																											{
																												if (array == null)
																													{
																														return [];
																													}

																													for (var oIndex = 0; oIndex < array.length; oIndex++)
																														{
																															var oItem = array[oIndex];
																															for (var iIndex = oIndex + 1; iIndex < array.length; iIndex++)
																																{
																																	var iItem = array[iIndex];
																																	var isOrdered = comparison(oItem, iItem);
																																	if (isOrdered == direction)
																																		{
																																			array[iIndex] = oItem;
																																			array[oIndex] = iItem;
																																			oItem = iItem;
																																		}
																																	}
																																}

																																return array;
																															}
