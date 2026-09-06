@props([
    'name' => 'no_tlp',
    'model' => null,
    'disabled' => 'false',
    'value' => '',
    'id' => null
])

@php
    $inputId = $id ?? 'phone_input_' . uniqid();
@endphp

<div x-data='{
    open: false,
    search: "",
    disabled: {{ $disabled ? (is_string($disabled) && !in_array($disabled, ["true", "false"]) ? $disabled : ($disabled === "true" || $disabled === true ? "true" : "false")) : "false" }},
    countries: [
        { code: "ID", dial: "+62", name: "Indonesia", flag: "🇮🇩", placeholder: "812-3456-7890" },
        { code: "US", dial: "+1", name: "United States", flag: "🇺🇸", placeholder: "201-555-5555" },
        { code: "CN", dial: "+86", name: "China (中国)", flag: "🇨🇳", placeholder: "138-0013-8000" },
        { code: "FR", dial: "+33", name: "France", flag: "🇫🇷", placeholder: "6 12 34 56 78" },
        { code: "IN", dial: "+91", name: "India (भारत)", flag: "🇮🇳", placeholder: "98765-43210" },
        { code: "GB", dial: "+44", name: "United Kingdom", flag: "🇬🇧", placeholder: "7911 123456" },
        { code: "MY", dial: "+60", name: "Malaysia", flag: "🇲🇾", placeholder: "12-345 6789" },
        { code: "SG", dial: "+65", name: "Singapore", flag: "🇸🇬", placeholder: "8123 4567" },
        { code: "JP", dial: "+81", name: "Japan (日本)", flag: "🇯🇵", placeholder: "90-1234-5678" },
        { code: "KR", dial: "+82", name: "South Korea (대한민국)", flag: "🇰🇷", placeholder: "10-1234-5678" },
        { code: "SA", dial: "+966", name: "Saudi Arabia (السعودية)", flag: "🇸🇦", placeholder: "50 123 4567" },
        { code: "AE", dial: "+971", name: "United Arab Emirates (الإمارات)", flag: "🇦🇪", placeholder: "50 123 4567" },
        { code: "AU", dial: "+61", name: "Australia", flag: "🇦🇺", placeholder: "412 345 678" },
        { code: "DE", dial: "+49", name: "Germany (Deutschland)", flag: "🇩🇪", placeholder: "1512 3456789" },
        { code: "NL", dial: "+31", name: "Netherlands", flag: "🇳🇱", placeholder: "6 12345678" },
        { code: "TH", dial: "+66", name: "Thailand (ไทย)", flag: "🇹🇭", placeholder: "81 234 5678" },
        { code: "VN", dial: "+84", name: "Vietnam (Việt Nam)", flag: "🇻🇳", placeholder: "91 234 5678" },
        { code: "PH", dial: "+63", name: "Philippines", flag: "🇵🇭", placeholder: "917 123 4567" },
        { code: "BR", dial: "+55", name: "Brazil (Brasil)", flag: "🇧🇷", placeholder: "11 98765-4321" },
        { code: "CA", dial: "+1", name: "Canada", flag: "🇨🇦", placeholder: "416-555-0199" },
        { code: "TR", dial: "+90", name: "Turkey (Türkiye)", flag: "🇹🇷", placeholder: "532 123 45 67" },
        { code: "RU", dial: "+7", name: "Russia (Россия)", flag: "🇷🇺", placeholder: "912 345-67-89" },
        { code: "ES", dial: "+34", name: "Spain (España)", flag: "🇪🇸", placeholder: "612 34 56 78" },
        { code: "IT", dial: "+39", name: "Italy (Italia)", flag: "🇮🇹", placeholder: "312 345 6789" },
        { code: "EG", dial: "+20", name: "Egypt (مصر)", flag: "🇪🇬", placeholder: "100 123 4567" }
    ],
    selectedCountry: { code: "ID", dial: "+62", name: "Indonesia", flag: "🇮🇩", placeholder: "812-3456-7890" },
    phoneNumber: "",
    
    init() {
        this.selectedCountry = this.countries[0];
        let initialVal = @if($model) {{ $model }} || @endif "{{ $value }}" || "";
        this.parsePhoneNumber(initialVal);

        @if($model)
        this.$watch("{{ $model }}", (newVal) => {
            if (newVal !== this.getFullNumber()) {
                this.parsePhoneNumber(newVal);
            }
        });
        @endif
    },

    parsePhoneNumber(val) {
        if (!val) {
            this.phoneNumber = "";
            return;
        }
        let clean = String(val).trim();
        let matched = false;
        for (let c of this.countries) {
            if (clean.startsWith(c.dial)) {
                this.selectedCountry = c;
                this.phoneNumber = clean.slice(c.dial.length).trim();
                matched = true;
                break;
            }
        }
        if (!matched) {
            if (clean.startsWith("0")) {
                this.selectedCountry = this.countries.find(c => c.code === "ID") || this.countries[0];
                this.phoneNumber = clean.slice(1).trim();
            } else {
                this.phoneNumber = clean;
            }
        }
    },

    selectCountry(c) {
        this.selectedCountry = c;
        this.open = false;
        this.search = "";
        this.syncValue();
        this.$nextTick(() => {
            if (this.$refs.phoneField) this.$refs.phoneField.focus();
        });
    },

    syncValue() {
        let full = this.getFullNumber();
        @if($model)
        {{ $model }} = full;
        @endif
        if (this.$refs.hiddenInput) {
            this.$refs.hiddenInput.value = full;
        }
    },

    getFullNumber() {
        let num = (this.phoneNumber || "").trim();
        if (!num) return "";
        if (num.startsWith("0")) {
            num = num.substring(1).trim();
        }
        return `${this.selectedCountry.dial} ${num}`;
    },

    get filteredCountries() {
        if (!this.search.trim()) return this.countries;
        let q = this.search.toLowerCase();
        return this.countries.filter(c => 
            c.name.toLowerCase().includes(q) || 
            c.dial.includes(q) || 
            c.code.toLowerCase().includes(q)
        );
    }
}' 
class="relative w-full"
@click.outside="open = false"
@keydown.escape.window="open = false">

    <!-- Container Input Group -->
    <div class="flex items-center rounded-xl border transition-all overflow-visible relative"
         :class="({{ $disabled }}) ? 'bg-slate-100 border-slate-200' : 'border-slate-200 bg-slate-50/70 focus-within:bg-white focus-within:ring-2 focus-within:ring-sky-500 focus-within:border-sky-500'">
        
        <!-- Country Selector Trigger Button -->
        <button type="button"
                @click="if (!({{ $disabled }})) open = !open"
                :disabled="{{ $disabled }}"
                :class="({{ $disabled }}) ? 'cursor-not-allowed opacity-75' : 'hover:bg-slate-100/90 cursor-pointer'"
                class="px-3 py-2.5 text-xs font-bold text-slate-700 bg-slate-100/90 border-r border-slate-200 shrink-0 flex items-center gap-2 transition-colors rounded-l-xl select-none focus:outline-none">
            <span class="text-base leading-none" x-text="selectedCountry.flag">🇮🇩</span>
            <span class="text-xs font-extrabold text-slate-800" x-text="selectedCountry.dial">+62</span>
            <i class="fa-solid fa-chevron-down text-[9px] text-slate-400 transition-transform duration-200"
               :class="open ? 'rotate-180 text-sky-600' : ''"></i>
        </button>

        <!-- Phone Number Text Input -->
        <input type="tel"
               x-ref="phoneField"
               x-model="phoneNumber"
               @input="syncValue()"
               :disabled="{{ $disabled }}"
               :placeholder="selectedCountry.placeholder"
               :class="({{ $disabled }}) ? 'text-slate-500 cursor-not-allowed font-bold' : 'text-slate-800 font-semibold'"
               class="w-full px-3.5 py-2.5 text-xs bg-transparent border-0 focus:outline-none rounded-r-xl">
        
        <!-- Hidden input for standard Form POST submission -->
        <input type="hidden" 
               name="{{ $name }}" 
               x-ref="hiddenInput" 
               :value="getFullNumber()">
    </div>

    <!-- Country Dropdown Popover (Matching screenshot) -->
    <div x-show="open"
         x-transition:enter="transition ease-out duration-150"
         x-transition:enter-start="opacity-0 translate-y-1 scale-98"
         x-transition:enter-end="opacity-100 translate-y-0 scale-100"
         x-transition:leave="transition ease-in duration-100"
         x-transition:leave-start="opacity-100 translate-y-0 scale-100"
         x-transition:leave-end="opacity-0 translate-y-1 scale-98"
         class="absolute z-50 top-full left-0 mt-1.5 w-72 sm:w-80 bg-white rounded-xl shadow-2xl border border-slate-200 overflow-hidden"
         style="display: none;">
        
        <!-- Search Field -->
        <div class="p-2 border-b border-slate-100 bg-slate-50/80 sticky top-0 z-10">
            <div class="relative">
                <i class="fa-solid fa-magnifying-glass text-slate-400 text-xs absolute left-3 top-1/2 -translate-y-1/2"></i>
                <input type="text"
                       x-model="search"
                       @keydown.enter.prevent
                       placeholder="Cari negara atau kode (+62)..."
                       class="w-full pl-8 pr-3 py-1.5 text-xs rounded-lg border border-slate-200 bg-white focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500 font-medium">
            </div>
        </div>

        <!-- Scrollable Country List -->
        <div class="max-h-56 overflow-y-auto divide-y divide-slate-50 py-1">
            <template x-for="c in filteredCountries" :key="c.code + c.dial">
                <button type="button"
                        @click="selectCountry(c)"
                        :class="selectedCountry.code === c.code && selectedCountry.dial === c.dial ? 'bg-[#00897B] text-white hover:bg-[#00796B]' : 'text-slate-700 hover:bg-slate-100'"
                        class="w-full px-3.5 py-2.5 text-xs flex items-center justify-between text-left transition-colors font-medium">
                    <div class="flex items-center gap-2.5 truncate pr-2">
                        <span class="text-base shrink-0 leading-none" x-text="c.flag"></span>
                        <span class="truncate" x-text="c.name"></span>
                    </div>
                    <span class="font-extrabold shrink-0 text-[11px]"
                          :class="selectedCountry.code === c.code && selectedCountry.dial === c.dial ? 'text-white/95' : 'text-slate-500'"
                          x-text="c.dial"></span>
                </button>
            </template>
            
            <div x-show="filteredCountries.length === 0" class="px-4 py-6 text-center text-xs text-slate-400">
                <i class="fa-solid fa-earth-americas text-slate-300 text-lg mb-1 block"></i>
                Negara tidak ditemukan
            </div>
        </div>
    </div>
</div>
