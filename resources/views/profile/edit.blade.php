<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Profile') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <!-- Admin Info Banner -->
            @if($user->isAdmin() && $admin)
                <div class="p-4 sm:p-8 bg-gradient-to-r from-yellow-50 to-orange-50 border border-yellow-200 shadow sm:rounded-lg">
                    <div class="max-w-xl">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Thông tin Quản Trị</h3>
                        <div class="grid grid-cols-2 gap-4 text-sm">
                            <div>
                                <span class="font-medium text-gray-700">Mã Admin:</span>
                                <span class="text-gray-900">{{ $admin->admin_code }}</span>
                            </div>
                            <div>
                                <span class="font-medium text-gray-700">Trạng thái:</span>
                                <span class="px-2 py-1 rounded text-xs font-semibold 
                                    {{ $admin->status === 'active' ? 'bg-green-100 text-green-800' : 
                                       ($admin->status === 'inactive' ? 'bg-gray-100 text-gray-800' : 'bg-red-100 text-red-800') }}">
                                    {{ $admin->status === 'active' ? 'Hoạt động' : 
                                       ($admin->status === 'inactive' ? 'Không hoạt động' : 'Tạm khóa') }}
                                </span>
                            </div>
                            @if($admin->department)
                                <div>
                                    <span class="font-medium text-gray-700">Phòng ban:</span>
                                    <span class="text-gray-900">{{ $admin->department }}</span>
                                </div>
                            @endif
                            @if($admin->position)
                                <div>
                                    <span class="font-medium text-gray-700">Chức vụ:</span>
                                    <span class="text-gray-900">{{ $admin->position }}</span>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @endif

            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-xl">
                    @include('profile.partials.update-profile-information-form', ['admin' => $admin ?? null])
                </div>
            </div>

            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-xl">
                    @include('profile.partials.update-password-form')
                </div>
            </div>

            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-xl">
                    @include('profile.partials.delete-user-form')
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
