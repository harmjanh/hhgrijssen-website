<template>
    <div class="bg-white rounded-lg shadow-lg p-6">
        <!-- Calendar Header -->
        <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6 gap-4">
            <div class="flex items-center space-x-4">
                <button @click="previousMonth" class="p-2 hover:bg-gray-100 rounded-full">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                </button>
                <h2 class="text-2xl font-semibold">{{ currentMonthName }} {{ currentYear }}</h2>
                <button @click="nextMonth" class="p-2 hover:bg-gray-100 rounded-full">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </button>
            </div>
            <div class="flex flex-col md:flex-row md:items-center gap-2 md:space-x-2 md:gap-0">
                <select v-model="selectedMonth" class="border rounded-md px-5 py-2 w-full md:min-w-[160px]">
                    <option v-for="month in months" :key="month.value" :value="month.value">
                        {{ month.label }}
                    </option>
                </select>
                <select v-model="selectedYear" class="border rounded-md px-5 py-2 w-full md:min-w-[120px]">
                    <option v-for="year in years" :key="year" :value="year">{{ year }}</option>
                </select>
                <button @click="goToSelectedDate" class="bg-blue-500 text-white px-4 py-2 rounded-md hover:bg-blue-600 w-full md:w-auto">
                    Ga
                </button>
                <button @click="goToCurrentMonth"
                    class="bg-gray-200 text-gray-700 px-4 py-2 rounded-md hover:bg-gray-300 w-full md:w-auto">
                    Huidige maand
                </button>
                <button @click="toggleView" class="bg-gray-200 text-gray-700 px-4 py-2 rounded-md hover:bg-gray-300 w-full md:w-auto">
                    {{ viewMode === 'month' ? 'Tijdlijn' : 'Maand' }}
                </button>
            </div>
        </div>

        <!-- Month View -->
        <div v-if="viewMode === 'month'" class="grid grid-cols-7 gap-px bg-gray-200">
            <!-- Week days header -->
            <div v-for="day in weekDays" :key="day" class="bg-gray-50 p-2 text-center font-semibold">
                {{ day }}
            </div>

            <!-- Calendar days -->
            <div v-for="(day, index) in calendarDays" :key="index" class="bg-white p-2 min-h-[100px]" :class="{
                'bg-gray-50': !day.isCurrentMonth,
                'border-l border-t border-r border-b': true
            }">
                <div class="flex justify-between items-start">
                    <span class="text-sm" :class="{
                        'text-gray-400': !day.isCurrentMonth,
                        'font-semibold': day.isToday
                    }">
                        {{ day.date }}
                    </span>
                </div>

                <!-- Agenda items for the day -->
                <div class="mt-1 space-y-1">
                    <div v-for="item in day.agendaItems" :key="item.id"
                        class="text-xs p-2 rounded cursor-pointer hover:bg-opacity-80 transition-colors" :class="{
                            'bg-blue-100 text-blue-800': !item.is_all_day,
                            'bg-purple-100 text-purple-800': item.is_all_day
                        }">
                        <div class="font-medium">{{ item.title }}</div>
                        <div v-if="item.description" class="text-xs opacity-75 line-clamp-2">
                            {{ item.description }}
                        </div>
                        <div v-if="!item.is_all_day" class="text-xs opacity-75 mt-1">
                            <span class="inline-flex items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 mr-1" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                {{ formatTimeRange(item.start_date, item.end_date) }}
                            </span>
                        </div>
                        <div v-if="item.location" class="text-xs opacity-75 mt-1">
                            <span class="inline-flex items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 mr-1" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                                {{ item.location }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Timeline View -->
        <div v-else class="space-y-4">
            <div v-for="day in timelineDays" :key="day.date" class="border rounded-lg p-4">
                <div class="flex justify-between items-center mb-2">
                    <h3 class="font-semibold">{{ formatTimelineDate(day.date) }}</h3>
                </div>
                <div class="space-y-2">
                    <div v-for="item in day.agendaItems" :key="item.id" class="p-3 rounded-lg" :class="{
                        'bg-blue-100 text-blue-800': !item.is_all_day,
                        'bg-purple-100 text-purple-800': item.is_all_day
                    }">
                        <div class="font-medium">{{ item.title }}</div>
                        <div v-if="item.description" class="text-sm opacity-75 mt-1">
                            {{ item.description }}
                        </div>
                        <div v-if="!item.is_all_day" class="text-sm opacity-75 mt-1">
                            <span class="inline-flex items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                {{ formatTimeRange(item.start_date, item.end_date) }}
                            </span>
                        </div>
                        <div v-if="item.location" class="text-sm opacity-75 mt-1">
                            <span class="inline-flex items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                                {{ item.location }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, onMounted, watch } from 'vue'
import axios from 'axios'

const currentDate = ref(new Date())
const selectedMonth = ref(new Date().getMonth())
const selectedYear = ref(new Date().getFullYear())
const agendaItems = ref([])
const viewMode = ref(localStorage.getItem('agendaViewMode') || 'timeline')

const weekDays = ['Zo', 'Ma', 'Di', 'Wo', 'Do', 'Vr', 'Za']
const months = [
    { value: 0, label: 'Januari' },
    { value: 1, label: 'Februari' },
    { value: 2, label: 'Maart' },
    { value: 3, label: 'April' },
    { value: 4, label: 'Mei' },
    { value: 5, label: 'Juni' },
    { value: 6, label: 'Juli' },
    { value: 7, label: 'Augustus' },
    { value: 8, label: 'September' },
    { value: 9, label: 'Oktober' },
    { value: 10, label: 'November' },
    { value: 11, label: 'December' }
]

const years = computed(() => {
    const currentYear = new Date().getFullYear()
    return Array.from({ length: 10 }, (_, i) => currentYear - 5 + i)
})

const currentMonthName = computed(() => {
    return months[currentDate.value.getMonth()].label
})

const currentYear = computed(() => {
    return currentDate.value.getFullYear()
})

const timelineDays = computed(() => {
    const year = currentDate.value.getFullYear()
    const month = currentDate.value.getMonth()
    const firstDay = new Date(year, month, 1)
    const lastDay = new Date(year, month + 1, 0)

    const days = []

    // Add all days of the current month
    for (let i = 1; i <= lastDay.getDate(); i++) {
        const date = new Date(year, month, i)
        const items = getAgendaItemsForDate(date)

        // Only include days that have agenda items
        if (items.length > 0) {
            days.push({
                date: date,
                agendaItems: items
            })
        }
    }

    return days
})

const calendarDays = computed(() => {
    const year = currentDate.value.getFullYear()
    const month = currentDate.value.getMonth()
    const firstDay = new Date(year, month, 1)
    const lastDay = new Date(year, month + 1, 0)

    const days = []

    // Add days from previous month
    const firstDayOfWeek = firstDay.getDay()
    for (let i = firstDayOfWeek - 1; i >= 0; i--) {
        const date = new Date(year, month, -i)
        days.push({
            date: date.getDate(),
            isCurrentMonth: false,
            isToday: false,
            agendaItems: getAgendaItemsForDate(date)
        })
    }

    // Add days from current month
    for (let i = 1; i <= lastDay.getDate(); i++) {
        const date = new Date(year, month, i)
        days.push({
            date: i,
            isCurrentMonth: true,
            isToday: isToday(date),
            agendaItems: getAgendaItemsForDate(date)
        })
    }

    // Add days from next month
    const remainingDays = 42 - days.length // 6 rows of 7 days
    for (let i = 1; i <= remainingDays; i++) {
        const date = new Date(year, month + 1, i)
        days.push({
            date: date.getDate(),
            isCurrentMonth: false,
            isToday: false,
            agendaItems: getAgendaItemsForDate(date)
        })
    }

    return days
})

function isToday(date) {
    const today = new Date()
    return date.getDate() === today.getDate() &&
        date.getMonth() === today.getMonth() &&
        date.getFullYear() === today.getFullYear()
}

function getAgendaItemsForDate(date) {
    return agendaItems.value.filter(item => {
        const itemStartDate = new Date(item.start_date);
        const itemEndDate = new Date(item.end_date);

        // Set all dates to start of day for proper comparison
        const compareDate = new Date(date.getFullYear(), date.getMonth(), date.getDate());
        const startDate = new Date(itemStartDate.getFullYear(), itemStartDate.getMonth(), itemStartDate.getDate());
        const endDate = new Date(itemEndDate.getFullYear(), itemEndDate.getMonth(), itemEndDate.getDate());

        // Check if the date falls within the item's date range
        return compareDate >= startDate && compareDate <= endDate;
    });
}

function formatTime(time) {
    if (!time) return ''
    const date = new Date(time)
    return date.toLocaleTimeString('nl-NL', { hour: '2-digit', minute: '2-digit' })
}

function formatTimeRange(startDate, endDate) {
    const start = new Date(startDate);
    const end = new Date(endDate);

    // If start and end are on the same day, show time range
    if (start.toDateString() === end.toDateString()) {
        return `${start.toLocaleTimeString('nl-NL', { hour: '2-digit', minute: '2-digit' })} - ${end.toLocaleTimeString('nl-NL', { hour: '2-digit', minute: '2-digit' })}`;
    }

    // If different days, show start time
    return start.toLocaleTimeString('nl-NL', { hour: '2-digit', minute: '2-digit' });
}

function formatTimelineDate(date) {
    const options = { weekday: 'long', day: 'numeric', month: 'long' };
    return date.toLocaleDateString('nl-NL', options);
}

function toggleView() {
    viewMode.value = viewMode.value === 'month' ? 'timeline' : 'month';
    // Store the preference in localStorage
    localStorage.setItem('agendaViewMode', viewMode.value);
}

async function fetchAgendaItems() {
    const year = currentDate.value.getFullYear()
    const month = currentDate.value.getMonth()
    const firstDay = new Date(year, month, 1)
    const lastDay = new Date(year, month + 1, 0)

    try {
        const response = await axios.get('/api/agenda/items', {
            params: {
                start_date: firstDay.toISOString().split('T')[0],
                end_date: lastDay.toISOString().split('T')[0]
            }
        })
        agendaItems.value = response.data
    } catch (error) {
        console.error('Error fetching agenda items:', error)
    }
}

function previousMonth() {
    currentDate.value = new Date(
        currentDate.value.getFullYear(),
        currentDate.value.getMonth() - 1,
        1
    )
}

function nextMonth() {
    currentDate.value = new Date(
        currentDate.value.getFullYear(),
        currentDate.value.getMonth() + 1,
        1
    )
}

function goToSelectedDate() {
    currentDate.value = new Date(selectedYear.value, selectedMonth.value, 1)
}

function goToCurrentMonth() {
    const now = new Date();
    currentDate.value = new Date(now.getFullYear(), now.getMonth(), 1);
    selectedMonth.value = now.getMonth();
    selectedYear.value = now.getFullYear();
}

// Watch for changes in the current date to fetch new agenda items
watch(currentDate, () => {
    fetchAgendaItems()
})

onMounted(() => {
    // Initialize the calendar with current date
    selectedMonth.value = currentDate.value.getMonth()
    selectedYear.value = currentDate.value.getFullYear()
    fetchAgendaItems()
})
</script>
