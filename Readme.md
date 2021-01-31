## Code Refactoring Task

#### Refactored below files

```1) app/Http/Controllers/BookingController.php```

```2) app/Repository/BookingRepository.php```

##### Changes in ```app/Http/Controllers/BookingController.php```

1 - Modified `index` Method
2 - Modified `store` Method
3 - Modified `update` Method
4 - Modified `immediateJobEmail` Method
5 - Modified `getHistory` Method
6 - Modified `acceptJob` Method
7 - Modified `acceptJobWithId` Method
8 - Modified `cancelJob` Method
9 - Modified `endJob` Method
10 - Modified `customerNotCall` Method
11 - Modified `getPotentialJobs` Method
12 - Modified `distanceFeed` Method
13 - Added `handleDistanceFeed` Method
14 - Modified `reopen` Method
15 - Modified `resendNotifications` Method
16 - Modified `resendSMSNotifications` Method

##### created Global Variable ``$__authenticatedUser`` and saved the authenticated user in the constructor since it is used in entire controller so better to get it in the constructor

### Note:
Code can be more refactored no need to user redundant variables and no need to use if else it makes code complex
Better to use collections methods and create helper functions if the same code is repeating try to avoid repeating code
make code more re-usable by creating helper class or functions
replace if else with only if statement

I tried my best to refactor the code as per my thinking but to be honest it can be more refactored.

Thanks